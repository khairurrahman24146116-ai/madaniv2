<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Score;
use App\Models\ScoreComponent;
use App\Models\Student;
use App\Models\Subject;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller Score — Input nilai & kalkulasi Nilai Akhir.
 *
 * FR-3.1: Guru menginput nilai Tugas, PH (Kuis), UTS, UAS.
 * FR-3.2: Sistem mengkalkulasi Nilai Akhir (NA) otomatis berdasarkan bobot.
 */
class ScoreController extends Controller
{
    private function getTeacherSubjectIds(Request $request): ?array
    {
        $user = $request->user();
        if ($user->isAdmin()) {
            return null;
        }

        return $user->teacherSubjects()->pluck('subject_id')->unique()->values()->toArray();
    }

    public function index(Request $request): JsonResponse
    {
        $query = Score::with(['student.user', 'subject', 'teacher']);

        $user = $request->user();
        if (! $user->isAdmin()) {
            $mappings = $user->teacherSubjects()->get(['subject_id', 'classroom_id']);
            if ($mappings->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where(function ($query) use ($mappings) {
                    foreach ($mappings as $mapping) {
                        $query->orWhere(function ($query) use ($mapping) {
                            $query->where('subject_id', $mapping->subject_id)
                                ->whereHas('student', fn ($students) => $students->where('classroom_id', $mapping->classroom_id));
                        });
                    }
                });
            }
        }

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('component_code')) {
            $query->where('component_code', $request->component_code);
        }

        if ($request->has('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->has('classroom_id')) {
            $query->whereHas('student', fn ($q) => $q->where('classroom_id', $request->classroom_id));
        }

        $scores = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $scores,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'component_code' => 'required|in:tugas,ph,uts,uas',
            'value' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string|max:100',
            'semester' => 'required|in:ganjil,genap',
            'academic_year' => 'required|string|max:9',
        ]);

        $user = $request->user();
        $student = Student::with('classroom')->findOrFail($validated['student_id']);
        if (! $this->canManageStudentSubject($request, $student, (int) $validated['subject_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki mapping mengajar untuk mata pelajaran dan kelas siswa ini',
            ], 403);
        }

        $score = Score::create([...$validated, 'teacher_id' => $user->id]);
        $score->load(['student.user', 'subject']);

        return response()->json([
            'success' => true,
            'message' => 'Nilai berhasil disimpan',
            'data' => $score,
        ], 201);
    }

    /**
     * Input nilai secara batch untuk satu kelas + subjek + komponen.
     * Body: { subject_id, component_code, semester, academic_year, scores: [{student_id, value, description?}] }
     */
    public function batchStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'component_code' => 'required|in:tugas,ph,uts,uas',
            'semester' => 'required|in:ganjil,genap',
            'academic_year' => 'required|string|max:9',
            'scores' => 'required|array|min:1',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.value' => 'required|numeric|min:0|max:100',
            'scores.*.description' => 'nullable|string|max:100',
        ]);

        $user = $request->user();
        $studentIds = collect($validated['scores'])->pluck('student_id')->unique();
        $students = Student::with('classroom')->whereIn('id', $studentIds)->get();

        $invalidStudents = $students->filter(function ($student) use ($request, $validated) {
            return ! $this->canManageStudentSubject($request, $student, (int) $validated['subject_id']);
        });

        if ($invalidStudents->isNotEmpty()) {
            $names = $invalidStudents->pluck('name')->join(', ');

            return response()->json([
                'success' => false,
                'message' => "Anda tidak memiliki mapping mengajar untuk siswa berikut: {$names}",
            ], 403);
        }

        $created = DB::transaction(function () use ($validated, $user) {
            $results = [];
            foreach ($validated['scores'] as $item) {
                $results[] = Score::create([
                    'student_id' => $item['student_id'],
                    'subject_id' => $validated['subject_id'],
                    'component_code' => $validated['component_code'],
                    'value' => $item['value'],
                    'description' => $item['description'] ?? null,
                    'teacher_id' => $user->id,
                    'semester' => $validated['semester'],
                    'academic_year' => $validated['academic_year'],
                ]);
            }

            return $results;
        });

        $ids = collect($created)->pluck('id');
        $scores = Score::with(['student.user', 'subject'])->whereIn('id', $ids)->get();

        return response()->json([
            'success' => true,
            'message' => count($created).' nilai berhasil disimpan',
            'data' => $scores,
        ], 201);
    }

    /**
     * Menampilkan detail nilai.
     */
    public function show(Score $score): JsonResponse
    {
        $this->authorizeScore($score);

        $score->load(['student.user', 'subject', 'teacher']);

        return response()->json([
            'success' => true,
            'data' => $score,
        ]);
    }

    public function update(Request $request, Score $score): JsonResponse
    {
        $this->authorizeScore($score);

        $validated = $request->validate([
            'value' => 'sometimes|numeric|min:0|max:100',
            'description' => 'nullable|string|max:100',
            'component_code' => 'sometimes|in:tugas,ph,uts,uas',
        ]);

        $score->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Nilai berhasil diperbarui',
            'data' => $score->fresh()->load(['student.user', 'subject']),
        ]);
    }

    public function destroy(Score $score): JsonResponse
    {
        $this->authorizeScore($score);

        $score->delete();

        return response()->json([
            'success' => true,
            'message' => 'Nilai berhasil dihapus',
        ]);
    }

    private function authorizeScore(Score $score): void
    {
        $user = request()->user();
        if ($user->isAdmin()) {
            return;
        }
        $score->loadMissing('student');
        if (! $this->canManageStudentSubject(request(), $score->student, $score->subject_id)) {
            abort(403, 'Anda tidak memiliki akses ke nilai ini');
        }
    }

    private function canManageStudentSubject(Request $request, Student $student, int $subjectId): bool
    {
        $user = $request->user();

        return $user->isAdmin() || $user->teacherSubjects()
            ->where('subject_id', $subjectId)
            ->where('classroom_id', $student->classroom_id)
            ->exists();
    }

    /**
     * FR-3.2: Kalkulasi Nilai Akhir (NA) otomatis.
     *
     * Menghitung NA berdasarkan bobot komponen yang dikonfigurasi Admin.
     *
     * Query params: student_id, subject_id, semester, academic_year
     *
     * Rumus:
     *   NA = (rata_tugas * weight_tugas/100)
     *      + (rata_ph * weight_ph/100)
     *      + (uts * weight_uts/100)
     *      + (uas * weight_uas/100)
     */
    public function finalGrade(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'semester' => 'required|in:ganjil,genap',
            'academic_year' => 'required|string|max:9',
        ]);

        $user = $request->user();
        $student = Student::with('classroom')->findOrFail($request->student_id);
        if (! $user->isAdmin()) {
            if (! $this->canManageStudentSubject($request, $student, (int) $request->subject_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke nilai siswa ini untuk mata pelajaran ini',
                ], 403);
            }
        }

        $studentId = $request->student_id;
        $subjectId = $request->subject_id;
        $semester = $request->semester;
        $academicYear = $request->academic_year;

        // Ambil bobot komponen untuk mapel & semester ini
        $components = ScoreComponent::where('subject_id', $subjectId)
            ->where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->get()
            ->keyBy('code');

        if ($components->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Bobot komponen belum dikonfigurasi untuk mapel & semester ini',
            ], 422);
        }

        // Ambil semua nilai siswa untuk mapel & semester ini
        $scores = Score::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->get();

        // Hitung rata-rata per komponen
        $averages = $scores->groupBy('component_code')->map(function ($group) {
            return $group->avg('value');
        });

        // Hitung NA berdasarkan bobot
        $detail = [];
        $totalWeighted = 0;
        $totalWeight = 0;

        foreach (['tugas', 'ph', 'uts', 'uas'] as $code) {
            $component = $components->get($code);
            $avgScore = $averages->get($code);
            $weight = $component?->weight ?? 0;

            $weightedScore = $avgScore !== null ? round($avgScore * $weight / 100, 2) : null;

            $detail[$code] = [
                'name' => $component?->name ?? ucfirst($code),
                'weight' => $weight,
                'average_score' => $avgScore ? round($avgScore, 2) : null,
                'weighted_score' => $weightedScore,
                'count' => $scores->where('component_code', $code)->count(),
            ];

            if ($weightedScore !== null) {
                $totalWeighted += $weightedScore;
                $totalWeight += $weight;
            }
        }

        $finalGrade = $totalWeight > 0 ? round($totalWeighted, 2) : null;

        return response()->json([
            'success' => true,
            'data' => [
                'student' => Student::with('user')->find($studentId)->only(['id', 'name', 'nis']),
                'subject' => Subject::find($subjectId)->only(['id', 'name', 'code']),
                'semester' => $semester,
                'academic_year' => $academicYear,
                'components' => $detail,
                'total_weight' => $totalWeight,
                'final_grade' => $finalGrade,
                'passed' => $finalGrade !== null ? $finalGrade >= 75 : null,
            ],
        ]);
    }

    /**
     * FR-3.2: Kalkulasi NA untuk semua siswa dalam satu kelas + mapel.
     * Query params: classroom_id, subject_id, semester, academic_year
     */
    public function batchFinalGrade(Request $request): JsonResponse
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'semester' => 'required|in:ganjil,genap',
            'academic_year' => 'required|string|max:9',
        ]);

        $user = $request->user();
        if (! $user->isAdmin()) {
            $teacherSubject = $user->teacherSubjects()
                ->where('subject_id', $request->subject_id)
                ->where('classroom_id', $request->classroom_id)
                ->first();

            if (! $teacherSubject) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak mengajar mata pelajaran ini di kelas tersebut',
                ], 403);
            }
        }

        $students = Student::where('classroom_id', $request->classroom_id)
            ->where('is_active', true)
            ->with('user')
            ->orderBy('name')
            ->get();

        $results = $students->map(function ($student) use ($request) {
            // Hitung NA per siswa dengan memanggil logika yang sama
            $scores = Score::where('student_id', $student->id)
                ->where('subject_id', $request->subject_id)
                ->where('semester', $request->semester)
                ->where('academic_year', $request->academic_year)
                ->get();

            $components = ScoreComponent::where('subject_id', $request->subject_id)
                ->where('semester', $request->semester)
                ->where('academic_year', $request->academic_year)
                ->get()
                ->keyBy('code');

            if ($components->isEmpty() || $scores->isEmpty()) {
                return [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'nis' => $student->nis,
                    'final_grade' => null,
                    'passed' => null,
                ];
            }

            $averages = $scores->groupBy('component_code')->map(fn ($g) => $g->avg('value'));
            $totalWeighted = 0;
            $totalWeight = 0;

            foreach (['tugas', 'ph', 'uts', 'uas'] as $code) {
                $component = $components->get($code);
                $avgScore = $averages->get($code);
                $weight = $component?->weight ?? 0;
                if ($avgScore !== null) {
                    $totalWeighted += $avgScore * $weight / 100;
                    $totalWeight += $weight;
                }
            }

            $finalGrade = $totalWeight > 0 ? round($totalWeighted, 2) : null;

            return [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'nis' => $student->nis,
                'final_grade' => $finalGrade,
                'passed' => $finalGrade !== null ? $finalGrade >= 75 : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'subject' => Subject::find($request->subject_id)->only(['id', 'name', 'code']),
                'semester' => $request->semester,
                'academic_year' => $request->academic_year,
                'students' => $results,
            ],
        ]);
    }

    /**
     * Helper: hitung NA untuk satu siswa + satu mapel.
     */
    private function calculateSubjectFinalGrade(int $studentId, int $subjectId, string $semester, string $academicYear): ?array
    {
        $scores = Score::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->get();

        $components = ScoreComponent::where('subject_id', $subjectId)
            ->where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->get()
            ->keyBy('code');

        if ($components->isEmpty()) {
            return null;
        }

        $averages = $scores->groupBy('component_code')->map(fn ($g) => $g->avg('value'));
        $detail = [];
        $totalWeighted = 0;
        $totalWeight = 0;

        foreach (['tugas', 'ph', 'uts', 'uas'] as $code) {
            $component = $components->get($code);
            $avgScore = $averages->get($code);
            $weight = $component?->weight ?? 0;
            $weightedScore = $avgScore !== null ? round($avgScore * $weight / 100, 2) : null;

            $detail[$code] = [
                'name' => $component?->name ?? ucfirst($code),
                'weight' => $weight,
                'average_score' => $avgScore ? round($avgScore, 2) : null,
                'weighted_score' => $weightedScore,
                'count' => $scores->where('component_code', $code)->count(),
            ];

            if ($weightedScore !== null) {
                $totalWeighted += $weightedScore;
                $totalWeight += $weight;
            }
        }

        $finalGrade = $totalWeight > 0 ? round($totalWeighted, 2) : null;

        return [
            'components' => $detail,
            'total_weight' => $totalWeight,
            'final_grade' => $finalGrade,
            'passed' => $finalGrade !== null ? $finalGrade >= 75 : null,
        ];
    }

    /**
     * FR-3.3: E-Rapor komprehensif per siswa.
     *
     * Menggabungkan seluruh nilai bidang studi menjadi satu rapor komprehensif
     * untuk satu siswa per semester.
     *
     * Query params: student_id, semester, academic_year
     */
    public function rapor(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'semester' => 'required|in:ganjil,genap',
            'academic_year' => 'required|string|max:9',
        ]);

        $user = $request->user();
        $student = Student::with('classroom')->findOrFail($request->student_id);

        if ($user->isWaliMurid()) {
            $allowedIds = $user->students()->pluck('id')->toArray();
            if (! in_array($student->id, $allowedIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya bisa melihat rapor anak Anda sendiri',
                ], 403);
            }
        }
        $semester = $request->semester;
        $academicYear = $request->academic_year;

        // Ambil semua mapel yang dipelajari kelas ini
        $subjects = Subject::whereHas('teacherSubjects', fn ($q) => $q->where('classroom_id', $student->classroom_id)
        )->orderBy('name')->get();

        $subjectReports = [];
        $totalGrade = 0;
        $subjectCount = 0;

        foreach ($subjects as $subject) {
            $result = $this->calculateSubjectFinalGrade(
                $student->id, $subject->id, $semester, $academicYear
            );

            $subjectReports[] = [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'subject_code' => $subject->code,
                'components' => $result ? $result['components'] : null,
                'total_weight' => $result ? $result['total_weight'] : 0,
                'final_grade' => $result ? $result['final_grade'] : null,
                'passed' => $result ? $result['passed'] : null,
            ];

            if ($result && $result['final_grade'] !== null) {
                $totalGrade += $result['final_grade'];
                $subjectCount++;
            }
        }

        // Ambil ringkasan absensi untuk semester ini
        $attendanceSummary = Attendance::whereHas('student', fn ($q) => $q->where('id', $student->id)
        )->whereHas('schedule', fn ($q) => $q->whereHas('teacherSubject', fn ($qs) => $qs->where('classroom_id', $student->classroom_id)
        )
        )->whereBetween('date', $this->getSemesterDateRange($semester, $academicYear))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $overallAverage = $subjectCount > 0 ? round($totalGrade / $subjectCount, 2) : null;

        return response()->json([
            'success' => true,
            'data' => [
                'student' => $student->only(['id', 'name', 'nis']),
                'classroom' => $student->classroom->only(['id', 'name', 'grade']),
                'semester' => $semester,
                'academic_year' => $academicYear,
                'subjects' => $subjectReports,
                'overall_average' => $overallAverage,
                'passed_all' => $subjectCount > 0
                    ? collect($subjectReports)->every(fn ($s) => $s['passed'] !== false)
                    : null,
                'attendance' => [
                    'total' => $attendanceSummary->sum(),
                    'H' => (int) $attendanceSummary->get('H', 0),
                    'S' => (int) $attendanceSummary->get('S', 0),
                    'I' => (int) $attendanceSummary->get('I', 0),
                    'A' => (int) $attendanceSummary->get('A', 0),
                ],
                'generated_at' => now()->toDateTimeString(),
            ],
        ]);
    }

    /**
     * FR-3.4: Ekspor rapor ke PDF.
     *
     * Sama seperti rapor() tapi outputnya berupa file PDF yang bisa diunduh.
     * Query params: student_id, semester, academic_year
     */
    public function raporPdf(Request $request): Response
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'semester' => 'required|in:ganjil,genap',
            'academic_year' => 'required|string|max:9',
        ]);

        $user = $request->user();
        $student = Student::with('classroom.user')->findOrFail($request->student_id);

        if ($user->isWaliMurid()) {
            $allowedIds = $user->students()->pluck('id')->toArray();
            if (! in_array($student->id, $allowedIds)) {
                abort(403, 'Anda hanya bisa melihat rapor anak Anda sendiri');
            }
        }
        $semester = $request->semester;
        $academicYear = $request->academic_year;

        $subjects = Subject::whereHas('teacherSubjects', fn ($q) => $q->where('classroom_id', $student->classroom_id)
        )->orderBy('name')->get();

        $subjectReports = [];
        $totalGrade = 0;
        $subjectCount = 0;

        foreach ($subjects as $subject) {
            $result = $this->calculateSubjectFinalGrade(
                $student->id, $subject->id, $semester, $academicYear
            );

            $subjectReports[] = [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'subject_code' => $subject->code,
                'components' => $result ? $result['components'] : null,
                'total_weight' => $result ? $result['total_weight'] : 0,
                'final_grade' => $result ? $result['final_grade'] : null,
                'passed' => $result ? $result['passed'] : null,
            ];

            if ($result && $result['final_grade'] !== null) {
                $totalGrade += $result['final_grade'];
                $subjectCount++;
            }
        }

        $attendanceSummary = Attendance::whereHas('student', fn ($q) => $q->where('id', $student->id)
        )->whereHas('schedule', fn ($q) => $q->whereHas('teacherSubject', fn ($qs) => $qs->where('classroom_id', $student->classroom_id)
        )
        )->whereBetween('date', $this->getSemesterDateRange($semester, $academicYear))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $overallAverage = $subjectCount > 0 ? round($totalGrade / $subjectCount, 2) : null;

        $pdf = Pdf::loadView('pdf.rapor', [
            'student' => $student,
            'classroom' => $student->classroom,
            'semester' => $semester,
            'academic_year' => $academicYear,
            'subjects' => $subjectReports,
            'overall_average' => $overallAverage,
            'subjectCount' => $subjectCount,
            'attendance' => [
                'total' => $attendanceSummary->sum(),
                'H' => (int) $attendanceSummary->get('H', 0),
                'S' => (int) $attendanceSummary->get('S', 0),
                'I' => (int) $attendanceSummary->get('I', 0),
                'A' => (int) $attendanceSummary->get('A', 0),
            ],
            'generated_at' => now()->toDateTimeString(),
        ]);

        $filename = "rapor_{$student->nis}_{$semester}_{$academicYear}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Ekspor data nilai ke CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Score::with(['student', 'subject', 'teacher']);

        $subjectIds = $this->getTeacherSubjectIds($request);
        if ($subjectIds !== null) {
            $query->whereIn('subject_id', $subjectIds);
        }

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->has('semester')) {
            $query->where('semester', $request->semester);
        }
        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        if ($request->has('classroom_id')) {
            $query->whereHas('student', fn ($q) => $q->where('classroom_id', $request->classroom_id));
        }

        $scores = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="nilai.csv"',
        ];

        $callback = function () use ($scores) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['NIS', 'Nama Siswa', 'Kelas', 'Mapel', 'Komponen', 'Nilai', 'Semester', 'TA', 'Guru', 'Tanggal Input']);

            foreach ($scores as $score) {
                fputcsv($file, [
                    $score->student->nis,
                    $score->student->name,
                    $score->student->classroom->name ?? '',
                    $score->subject->name,
                    $score->component_code,
                    $score->value,
                    $score->semester,
                    $score->academic_year,
                    $score->teacher->name ?? '',
                    $score->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getSemesterDateRange(string $semester, string $academicYear): array
    {
        [$year1, $year2] = explode('/', $academicYear);
        if ($semester === 'ganjil') {
            return ["{$year1}-07-01", "{$year1}-12-31"];
        }

        return ["{$year2}-01-01", "{$year2}-06-30"];
    }
}
