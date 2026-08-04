<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClassroomRequest;
use App\Http\Requests\UpdateClassroomRequest;
use App\Models\Classroom;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller Classroom — CRUD data kelas.
 *
 * Fase 1 (FR-1.2): Mengelola kelas dalam 3 tingkatan (X, XI, XII).
 * Setiap kelas memiliki daftar siswa dan mapping guru-mata pelajaran.
 */
class ClassroomController extends Controller
{
    /**
     * Menampilkan daftar kelas dengan jumlah siswa.
     * Filter: grade (X/XI/XII) dan academic_year.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Classroom::withCount('students');

        // RBAC: guru hanya melihat kelas yang dia ajar; wali hanya melihat kelas anaknya.
        $user = $request->user();
        if ($user->isGuru()) {
            $classroomIds = $user->teacherSubjects()->pluck('classroom_id')->unique();
            $query->whereIn('id', $classroomIds);
        } elseif ($user->isWaliMurid()) {
            $classroomIds = $user->students()->pluck('classroom_id')->unique();
            $query->whereIn('id', $classroomIds);
        }

        // Filter berdasarkan tingkat kelas
        if ($request->has('grade')) {
            $query->where('grade', $request->grade);
        }

        // Filter berdasarkan tahun ajaran
        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $classrooms = $query->orderBy('grade')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $classrooms,
        ]);
    }

    private function canAccessClassroom(Request $request, Classroom $classroom): bool
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isGuru()) {
            return $user->teacherSubjects()
                ->where('classroom_id', $classroom->id)
                ->exists();
        }

        if ($user->isWaliMurid()) {
            return $user->students()->where('classroom_id', $classroom->id)->exists();
        }

        return false;
    }

    /**
     * Membuat kelas baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'grade' => 'required|in:X,XI,XII',
            'academic_year' => 'required|string|max:9',
            'description' => 'nullable|string',
        ]);

        $classroom = Classroom::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil dibuat',
            'data' => $classroom,
        ], 201);
    }

    /**
     * Menampilkan detail kelas (siswa, guru, mata pelajaran).
     */
    public function show(Request $request, Classroom $classroom): JsonResponse
    {
        if (! $this->canAccessClassroom($request, $classroom)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Anda tidak berhak mengakses kelas ini.',
            ], 403);
        }

        $classroom->load(['students', 'teacherSubjects.subject', 'teacherSubjects.user']);

        return response()->json([
            'success' => true,
            'data' => $classroom,
        ]);
    }

    /**
     * Memperbarui data kelas.
     */
    public function update(Request $request, Classroom $classroom): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:50',
            'grade' => 'sometimes|in:X,XI,XII',
            'academic_year' => 'sometimes|string|max:9',
            'description' => 'nullable|string',
        ]);

        $classroom->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil diperbarui',
            'data' => $classroom,
        ]);
    }

    /**
     * Menghapus kelas.
     * Dilarang menghapus kelas yang masih memiliki siswa terdaftar.
     */
    public function destroy(Classroom $classroom): JsonResponse
    {
        if ($classroom->students()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus kelas yang masih memiliki siswa',
            ], 422);
        }

        $classroom->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil dihapus',
        ]);
    }

    /**
     * Menampilkan daftar siswa aktif di kelas tertentu.
     * Berguna untuk absensi atau melihat anggota kelas.
     */
    public function getActiveStudents(Request $request, Classroom $classroom): JsonResponse
    {
        if (! $this->canAccessClassroom($request, $classroom)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Anda tidak berhak mengakses siswa di kelas ini.',
            ], 403);
        }

        $students = $classroom->activeStudents()->with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    /**
     * Web (admin): daftar kelas.
     */
    public function webIndex(): View
    {
        $classrooms = Classroom::with('waliKelas')->withCount('students')->orderBy('grade')->orderBy('name')->paginate(50);

        return view('admin.classrooms.index', compact('classrooms'));
    }

    /**
     * Web (admin): form tambah kelas.
     */
    public function webCreate(): View
    {
        $gurus = User::where('role', 'guru')->orderBy('name')->get();

        return view('admin.classrooms.create', compact('gurus'));
    }

    /**
     * Web (admin): simpan kelas baru.
     */
    public function webStore(StoreClassroomRequest $request): RedirectResponse
    {
        $data = $request->validated();
        Classroom::create($data);
        ActivityLogger::log('create', 'Menambahkan kelas: '.$data['name']);

        return redirect()->route('admin.classrooms.index')->with('success', 'Kelas berhasil ditambahkan');
    }

    /**
     * Web (admin): form edit kelas.
     */
    public function webEdit(Classroom $classroom): View
    {
        $gurus = User::where('role', 'guru')->orderBy('name')->get();

        return view('admin.classrooms.edit', compact('classroom', 'gurus'));
    }

    /**
     * Web (admin): perbarui kelas.
     */
    public function webUpdate(UpdateClassroomRequest $request, Classroom $classroom): RedirectResponse
    {
        $data = $request->validated();
        $classroom->update($data);
        ActivityLogger::log('update', 'Mengubah kelas: '.$data['name'], $classroom);

        return redirect()->route('admin.classrooms.index')->with('success', 'Kelas berhasil diperbarui');
    }

    /**
     * Web (admin): hapus kelas.
     * Dilarang menghapus kelas yang masih memiliki siswa.
     */
    public function webDestroy(Classroom $classroom): RedirectResponse
    {
        if ($classroom->students()->count() > 0) {
            return back()->withErrors(['Kelas masih memiliki siswa']);
        }

        $name = $classroom->name;
        $classroom->delete();
        ActivityLogger::log('delete', 'Menghapus kelas: '.$name);

        return redirect()->route('admin.classrooms.index')->with('success', 'Kelas berhasil dihapus');
    }
}
