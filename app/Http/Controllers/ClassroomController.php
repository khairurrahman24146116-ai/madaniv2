<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function show(Classroom $classroom): JsonResponse
    {
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
    public function getActiveStudents(Classroom $classroom): JsonResponse
    {
        $students = $classroom->activeStudents()->with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }
}
