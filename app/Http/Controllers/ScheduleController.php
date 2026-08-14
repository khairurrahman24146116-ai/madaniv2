<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Schedule;
use App\Models\TeacherSubject;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller Schedule — CRUD jadwal pelajaran blok sore.
 *
 * Fase 1 (FR-1.3): Mengatur jadwal dalam rentang 14:00 - 16:00 WIB.
 * Setiap jadwal terkait dengan mapping guru-mata pelajaran-kelas.
 * Dilengkapi validasi bentrok jadwal di kelas yang sama.
 */
class ScheduleController extends Controller
{
    /**
     * Menampilkan daftar jadwal dengan filter.
     * Filter: day, teacher_subject_id, classroom_id.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Schedule::with(['teacherSubject.user', 'teacherSubject.subject', 'teacherSubject.classroom']);

        if ($request->has('day')) {
            $query->where('day', $request->day);
        }

        if ($request->has('teacher_subject_id')) {
            $query->where('teacher_subject_id', $request->teacher_subject_id);
        }

        if ($request->has('classroom_id')) {
            $query->whereHas('teacherSubject', fn ($q) => $q->where('classroom_id', $request->classroom_id));
        }

        $schedules = $query->orderBy('day')->orderBy('start_time')->get();

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    /**
     * Membuat jadwal baru.
     * Validasi: waktu harus dalam blok sore (14:00-16:00).
     * Validasi: guru tidak boleh bentrok jadwal di jam yang sama.
     * Validasi: kelas tidak boleh bentrok (dua mapel berbeda di jam sama).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'teacher_subject_id' => 'required|exists:teacher_subjects,id',
            'day' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'hour_order' => 'required|integer|min:1|max:4',
        ]);

        $startTime = $validated['start_time'];
        $endTime = $validated['end_time'];

        if ($startTime < '14:00' || $endTime > '16:00') {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal harus berada dalam rentang blok sore (14:00 - 16:00 WIB)',
            ], 422);
        }

        $teacherSubject = TeacherSubject::with('user')->findOrFail($validated['teacher_subject_id']);

        // Cek bentrok guru: guru tidak bisa mengajar 2 kelas di jam yang sama
        $guruTsIds = TeacherSubject::where('user_id', $teacherSubject->user_id)->pluck('id');
        $guruConflict = Schedule::whereIn('teacher_subject_id', $guruTsIds)
            ->where('day', $validated['day'])
            ->where('hour_order', $validated['hour_order'])
            ->exists();

        if ($guruConflict) {
            return response()->json([
                'success' => false,
                'message' => 'Guru sudah memiliki jadwal di jam tersebut',
            ], 422);
        }

        // Cek bentrok kelas: dua mapel berbeda tidak boleh di jam sama di kelas yang sama
        $classConflict = Schedule::whereHas('teacherSubject', fn ($q) => $q->where('classroom_id', $teacherSubject->classroom_id)
        )->where('day', $validated['day'])
            ->where('hour_order', $validated['hour_order'])
            ->exists();

        if ($classConflict) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah ada jadwal lain di kelas yang sama pada jam tersebut',
            ], 422);
        }

        $schedule = Schedule::create($validated);
        $schedule->load(['teacherSubject.user', 'teacherSubject.subject', 'teacherSubject.classroom']);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dibuat',
            'data' => $schedule,
        ], 201);
    }

    /**
     * Menampilkan detail jadwal.
     */
    public function show(Schedule $schedule): JsonResponse
    {
        $schedule->load(['teacherSubject.user', 'teacherSubject.subject', 'teacherSubject.classroom']);

        return response()->json([
            'success' => true,
            'data' => $schedule,
        ]);
    }

    /**
     * Memperbarui jadwal.
     * Tetap memvalidasi batasan blok sore 14:00-16:00.
     */
    public function update(Request $request, Schedule $schedule): JsonResponse
    {
        $validated = $request->validate([
            'teacher_subject_id' => 'sometimes|exists:teacher_subjects,id',
            'day' => 'sometimes|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'hour_order' => 'sometimes|integer|min:1|max:4',
        ]);

        if (isset($validated['start_time']) || isset($validated['end_time'])) {
            $start = $validated['start_time'] ?? $schedule->start_time;
            $end = $validated['end_time'] ?? $schedule->end_time;

            if ($start < '14:00' || $end > '16:00') {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal harus berada dalam rentang blok sore (14:00 - 16:00 WIB)',
                ], 422);
            }
        }

        $tsId = $validated['teacher_subject_id'] ?? $schedule->teacher_subject_id;
        $day = $validated['day'] ?? $schedule->day;
        $hourOrder = $validated['hour_order'] ?? $schedule->hour_order;

        // Jika ada perubahan yang mempengaruhi bentrok
        if (isset($validated['teacher_subject_id']) || isset($validated['day']) || isset($validated['hour_order'])) {
            $teacherSubject = TeacherSubject::with('user')->findOrFail($tsId);

            $guruTsIds = TeacherSubject::where('user_id', $teacherSubject->user_id)->pluck('id');
            $guruConflict = Schedule::whereIn('teacher_subject_id', $guruTsIds)
                ->where('day', $day)
                ->where('hour_order', $hourOrder)
                ->where('id', '!=', $schedule->id)
                ->exists();

            if ($guruConflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Guru sudah memiliki jadwal di jam tersebut',
                ], 422);
            }

            $classConflict = Schedule::whereHas('teacherSubject', fn ($q) => $q->where('classroom_id', $teacherSubject->classroom_id)
            )->where('day', $day)
                ->where('hour_order', $hourOrder)
                ->where('id', '!=', $schedule->id)
                ->exists();

            if ($classConflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sudah ada jadwal lain di kelas yang sama pada jam tersebut',
                ], 422);
            }
        }

        $schedule->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil diperbarui',
            'data' => $schedule->fresh()->load(['teacherSubject.user', 'teacherSubject.subject', 'teacherSubject.classroom']),
        ]);
    }

    /**
     * Menghapus jadwal.
     */
    public function destroy(Schedule $schedule): JsonResponse
    {
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dihapus',
        ]);
    }

    /**
     * Menampilkan jadwal per hari untuk kelas tertentu.
     * Digunakan untuk melihat KBM di suatu kelas pada hari tertentu.
     */
    public function getDaySchedule(Request $request): JsonResponse
    {
        $request->validate([
            'day' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        $schedules = Schedule::where('day', $request->day)
            ->whereHas('teacherSubject', fn ($q) => $q->where('classroom_id', $request->classroom_id))
            ->with(['teacherSubject.user', 'teacherSubject.subject'])
            ->orderBy('hour_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    /**
     * Web (guru): jadwal semua kelas sebagai grid.
     */
    public function webIndex(): View
    {
        $schedules = Schedule::with('teacherSubject.subject', 'teacherSubject.classroom', 'teacherSubject.user')->get();
        $scheduleGrid = $schedules->map(fn ($s) => [
            'day' => $s->day,
            'start_time' => $s->start_time,
            'end_time' => $s->end_time,
            'subject' => $s->teacherSubject->subject->name ?? '',
            'teacher' => $s->teacherSubject->user->name ?? '',
            'teacher_short' => substr($s->teacherSubject->user->name ?? '', 0, 10),
            'room' => 'Ruang '.($s->hour_order + 1),
        ]);

        return view('schedules.index', compact('scheduleGrid'));
    }

    /**
     * Web (guru): jadwal versi mobile per hari.
     */
    public function webMobile(): View
    {
        $day = request('day', now()->locale('id')->isoFormat('dddd'));
        $currentDay = strtolower($day);
        $schedules = Schedule::with('teacherSubject.subject', 'teacherSubject.classroom', 'teacherSubject.user')
            ->where('day', $day)
            ->orWhereNull('day')
            ->get();

        return view('schedules.mobile', compact('schedules', 'currentDay'));
    }

    /**
     * Web (admin): daftar jadwal.
     */
    public function webAdminIndex(): View
    {
        $schedules = Schedule::with('teacherSubject.user', 'teacherSubject.subject', 'teacherSubject.classroom')
            ->orderBy('day')->orderBy('hour_order')->paginate(50);

        return view('admin.schedules.index', compact('schedules'));
    }

    /**
     * Web (admin): form tambah jadwal.
     */
    public function webAdminCreate(): View
    {
        $mappings = TeacherSubject::with('user', 'subject', 'classroom')->orderBy('classroom_id')->get();

        return view('admin.schedules.create', compact('mappings'));
    }

    /**
     * Web (admin): simpan jadwal baru.
     */
    public function webAdminStore(StoreScheduleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        Schedule::create($data);
        ActivityLogger::log('create', 'Menambahkan jadwal: '.ucfirst($data['day']).' jam '.$data['start_time']);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil ditambahkan');
    }

    /**
     * Web (admin): form edit jadwal.
     */
    public function webAdminEdit(Schedule $schedule): View
    {
        $mappings = TeacherSubject::with('user', 'subject', 'classroom')->orderBy('classroom_id')->get();

        return view('admin.schedules.edit', compact('schedule', 'mappings'));
    }

    /**
     * Web (admin): perbarui jadwal.
     */
    public function webAdminUpdate(UpdateScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        $data = $request->validated();
        $schedule->update($data);
        ActivityLogger::log('update', 'Mengubah jadwal: '.ucfirst($data['day']).' jam '.$data['start_time'], $schedule);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui');
    }

    /**
     * Web (admin): hapus jadwal.
     */
    public function webAdminDestroy(Schedule $schedule): RedirectResponse
    {
        $day = $schedule->day;
        $time = $schedule->start_time;
        $schedule->delete();
        ActivityLogger::log('delete', 'Menghapus jadwal: '.ucfirst($day).' jam '.$time);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus');
    }
}
