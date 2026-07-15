<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Student::with(['classroom', 'user']);

        if ($request->has('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        if ($request->has('grade')) {
            $query->whereHas('classroom', fn($q) => $q->where('grade', $request->grade));
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'nis' => 'required|string|max:20|unique:students,nis',
            'name' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'parent_name' => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:users,email',
        ]);

        $student = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? "siswa{$validated['nis']}@madani.id",
                'password' => bcrypt('siswa123'),
                'role' => 'wali_murid',
            ]);

            return Student::create([
                ...$validated,
                'user_id' => $user->id,
            ]);
        });

        $student->load(['classroom', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil ditambahkan',
            'data' => $student,
        ], 201);
    }

    public function show(Student $student): JsonResponse
    {
        $student->load(['classroom', 'user']);

        return response()->json([
            'success' => true,
            'data' => $student,
        ]);
    }

    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'classroom_id' => 'sometimes|exists:classrooms,id',
            'nis' => 'sometimes|string|max:20|unique:students,nis,' . $student->id,
            'name' => 'sometimes|string|max:100',
            'gender' => 'sometimes|in:L,P',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'parent_name' => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean',
        ]);

        $student->update($validated);

        if (isset($validated['name'])) {
            $student->user->update(['name' => $validated['name']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diperbarui',
            'data' => $student->fresh()->load(['classroom', 'user']),
        ]);
    }

    public function destroy(Student $student): JsonResponse
    {
        DB::transaction(function () use ($student) {
            $student->user->delete();
            $student->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil dihapus',
        ]);
    }

    public function bulkImport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'students' => 'required|array|min:1',
            'students.*.nis' => 'required|string|max:20',
            'students.*.name' => 'required|string|max:100',
            'students.*.gender' => 'required|in:L,P',
        ]);

        $imported = 0;
        $skipped = 0;

        foreach ($validated['students'] as $data) {
            if (Student::where('nis', $data['nis'])->exists()) {
                $skipped++;
                continue;
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => "siswa{$data['nis']}@madani.id",
                'password' => bcrypt('siswa123'),
                'role' => 'wali_murid',
            ]);

            Student::create([
                ...$data,
                'classroom_id' => $validated['classroom_id'],
                'user_id' => $user->id,
            ]);

            $imported++;
        }

        return response()->json([
            'success' => true,
            'message' => "Import selesai: {$imported} siswa ditambahkan, {$skipped} dilewati (NIS duplikat)",
            'data' => ['imported' => $imported, 'skipped' => $skipped],
        ]);
    }
}
