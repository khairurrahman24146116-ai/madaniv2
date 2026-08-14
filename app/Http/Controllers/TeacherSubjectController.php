<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherSubjectRequest;
use App\Http\Requests\UpdateTeacherSubjectRequest;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller TeacherSubject — Mapping Guru-Mata Pelajaran-Kelas.
 *
 * Fase 1 (FR-1.1): Menghubungkan siapa mengajar apa di kelas mana.
 * Satu guru bisa mengajar banyak mapel di banyak kelas.
 * Satu mapel bisa diajar oleh banyak guru di kelas berbeda.
 */
class TeacherSubjectController extends Controller
{
    /**
     * Menampilkan daftar mapping dengan filter.
     * Filter: user_id (guru), subject_id, classroom_id.
     */
    public function index(Request $request): JsonResponse
    {
        $query = TeacherSubject::with(['user', 'subject', 'classroom']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        $mappings = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $mappings,
        ]);
    }

    /**
     * Membuat mapping baru.
     * Validasi: user harus berrole guru.
     * Validasi: kombinasi (guru, mapel, kelas) tidak boleh duplikat.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        // Pastikan user yang dipilih benar-benar seorang guru
        $guru = User::findOrFail($validated['user_id']);
        if ($guru->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'User yang dipilih bukan guru',
            ], 422);
        }

        // Cegah duplikasi mapping
        $exists = TeacherSubject::where([
            'user_id' => $validated['user_id'],
            'subject_id' => $validated['subject_id'],
            'classroom_id' => $validated['classroom_id'],
        ])->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Mapping guru-mata pelajaran-kelas ini sudah ada',
            ], 422);
        }

        $mapping = TeacherSubject::create($validated);
        $mapping->load(['user', 'subject', 'classroom']);

        return response()->json([
            'success' => true,
            'message' => 'Mapping guru berhasil dibuat',
            'data' => $mapping,
        ], 201);
    }

    /**
     * Memperbarui mapping guru-mata pelajaran-kelas.
     */
    public function update(Request $request, TeacherSubject $teacherSubject): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        $guru = User::findOrFail($validated['user_id']);
        if ($guru->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'User yang dipilih bukan guru',
            ], 422);
        }

        $exists = TeacherSubject::where([
            'user_id' => $validated['user_id'],
            'subject_id' => $validated['subject_id'],
            'classroom_id' => $validated['classroom_id'],
        ])->where('id', '!=', $teacherSubject->id)->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Mapping guru-mata pelajaran-kelas ini sudah ada',
            ], 422);
        }

        $teacherSubject->update($validated);
        $teacherSubject->load(['user', 'subject', 'classroom']);

        return response()->json([
            'success' => true,
            'message' => 'Mapping guru berhasil diperbarui',
            'data' => $teacherSubject,
        ]);
    }

    /**
     * Menampilkan detail mapping beserta jadwal terkait.
     */
    public function show(TeacherSubject $teacherSubject): JsonResponse
    {
        $teacherSubject->load(['user', 'subject', 'classroom', 'schedules']);

        return response()->json([
            'success' => true,
            'data' => $teacherSubject,
        ]);
    }

    /**
     * Menghapus mapping.
     * Dilarang menghapus mapping yang sudah memiliki jadwal.
     */
    public function destroy(TeacherSubject $teacherSubject): JsonResponse
    {
        if ($teacherSubject->schedules()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus mapping yang sudah memiliki jadwal',
            ], 422);
        }

        $teacherSubject->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mapping guru berhasil dihapus',
        ]);
    }

    /**
     * Menampilkan jadwal mengajar seorang guru pada hari tertentu.
     * Digunakan oleh guru untuk melihat jadwal hari ini.
     */
    public function getTeacherSchedule(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'day' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
        ]);

        $schedules = TeacherSubject::where('user_id', $request->user_id)
            ->with(['subject', 'classroom', 'schedules'])
            ->get()
            ->flatMap(fn ($ts) => $ts->schedules->where('day', $request->day)->map(fn ($s) => [
                'schedule_id' => $s->id,
                'subject' => $ts->subject->name,
                'classroom' => $ts->classroom->name,
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'hour_order' => $s->hour_order,
            ]))
            ->sortBy('hour_order')
            ->values();

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    /**
     * Web (admin): daftar mapping guru-mapel-kelas.
     */
    public function webIndex(): View
    {
        $mappings = TeacherSubject::with('user', 'subject', 'classroom')->withCount('schedules')->orderBy('classroom_id')->paginate(50);

        return view('admin.teacher-subjects.index', compact('mappings'));
    }

    /**
     * Web (admin): form tambah mapping.
     */
    public function webCreate(): View
    {
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();

        return view('admin.teacher-subjects.create', compact('teachers', 'subjects', 'classrooms'));
    }

    /**
     * Web (admin): simpan mapping baru.
     */
    public function webStore(StoreTeacherSubjectRequest $request): RedirectResponse
    {
        $data = $request->validated();
        TeacherSubject::firstOrCreate($data);
        ActivityLogger::log('create', 'Menambahkan mapping guru-mapel-kelas');

        return redirect()->route('admin.teacher-subjects.index')->with('success', 'Mapping berhasil ditambahkan');
    }

    /**
     * Web (admin): form edit mapping.
     */
    public function webEdit(TeacherSubject $teacherSubject): View
    {
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();

        return view('admin.teacher-subjects.edit', compact('teacherSubject', 'teachers', 'subjects', 'classrooms'));
    }

    /**
     * Web (admin): perbarui mapping.
     */
    public function webUpdate(UpdateTeacherSubjectRequest $request, TeacherSubject $teacherSubject): RedirectResponse
    {
        $data = $request->validated();
        $teacherSubject->update($data);
        ActivityLogger::log('update', 'Mengubah mapping guru-mapel-kelas', $teacherSubject);

        return redirect()->route('admin.teacher-subjects.index')->with('success', 'Mapping berhasil diperbarui');
    }

    /**
     * Web (admin): hapus mapping.
     */
    public function webDestroy(TeacherSubject $teacherSubject): RedirectResponse
    {
        $teacherSubject->delete();
        ActivityLogger::log('delete', 'Menghapus mapping guru-mapel-kelas');

        return redirect()->route('admin.teacher-subjects.index')->with('success', 'Mapping berhasil dihapus');
    }
}
