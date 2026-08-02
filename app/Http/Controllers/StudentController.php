<?php

namespace App\Http\Controllers;

use App\Models\Score;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Controller Student — CRUD & import data siswa.
 *
 * Fase 1 (FR-1.2): Mengelola database siswa yang terbagi per kelas (X, XI, XII).
 * Setiap siswa memiliki akun User (role: wali_murid) untuk akses sistem.
 */
class StudentController extends Controller
{
    /**
     * Menampilkan daftar siswa dengan filter.
     * Filter: classroom_id, grade (X/XI/XII), is_active, search (nama/NIS).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Student::with(['classroom', 'user']);

        // RBAC: Guru hanya lihat siswa di kelas yang dia ajar
        $user = $request->user();
        if ($user->isGuru()) {
            $classroomIds = $user->teacherSubjects()->pluck('classroom_id')->unique();
            $query->whereIn('classroom_id', $classroomIds);
        }

        if ($request->has('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        if ($request->has('grade')) {
            $query->whereHas('classroom', fn ($q) => $q->where('grade', $request->grade));
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

    /**
     * Menambah siswa baru beserta akun user.
     * Transaksional: jika user berhasil dibuat, baru siswa dibuat.
     * Email default: siswa{NIS}@madani.id jika tidak diisi.
     * Password: random (Str::random(10)), user wajib ganti password saat login pertama.
     */
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
                'password' => Str::random(10),
                'role' => 'wali_murid',
                'must_change_password' => true,
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

    /**
     * Menampilkan detail siswa.
     */
    public function show(Student $student): JsonResponse
    {
        $student->load(['classroom', 'user']);

        return response()->json([
            'success' => true,
            'data' => $student,
        ]);
    }

    /**
     * Memperbarui data siswa.
     * Jika nama diubah, otomatis sinkron ke tabel users.
     */
    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'classroom_id' => 'sometimes|exists:classrooms,id',
            'nis' => 'sometimes|string|max:20|unique:students,nis,'.$student->id,
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

        // Sinkronisasi nama ke akun user
        if (isset($validated['name'])) {
            $student->user->update(['name' => $validated['name']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diperbarui',
            'data' => $student->fresh()->load(['classroom', 'user']),
        ]);
    }

    /**
     * Menghapus siswa beserta akun user-nya.
     * Diblokir jika siswa masih memiliki data nilai atau absensi.
     * Nonaktifkan siswa (is_active = false) sebagai alternatif.
     */
    public function destroy(Student $student): JsonResponse
    {
        $scoresCount = Score::where('student_id', $student->id)->count();
        $attendancesCount = $student->attendances()->count();

        if ($scoresCount > 0 || $attendancesCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Siswa tidak dapat dihapus karena masih memiliki {$scoresCount} data nilai dan {$attendancesCount} data absensi. Nonaktifkan siswa (set is_active = false) sebagai alternatif.",
            ], 422);
        }

        DB::transaction(function () use ($student) {
            $student->user->delete();
            $student->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil dihapus',
        ]);
    }

    /**
     * Import massal siswa untuk satu kelas.
     * Melewati data dengan NIS yang sudah terdaftar (tidak menimpa).
     * Berguna untuk migrasi data awal atau awal semester.
     */
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
            // Skip jika NIS sudah ada (mencegah duplikasi)
            if (Student::where('nis', $data['nis'])->exists()) {
                $skipped++;

                continue;
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => "siswa{$data['nis']}@madani.id",
                'password' => Str::random(10),
                'role' => 'wali_murid',
                'must_change_password' => true,
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
