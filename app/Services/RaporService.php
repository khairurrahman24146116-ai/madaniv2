<?php

namespace App\Services;

use App\Models\Score;
use App\Models\ScoreComponent;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Collection;

/**
 * Service Rapor — kalkulasi Nilai Akhir (NA) dan perakit laporan rapor.
 *
 * Satu-satunya sumber kalkulasi NA. Semua entry point (API rapor/PDF,
 * halaman wali-murid/rapor, halaman scores/rapor-preview) memakai service ini
 * supaya tidak ada implementasi kalkulasi yang berbeda-beda.
 */
class RaporService
{
    /**
     * Hitung NA untuk satu siswa + satu mapel pada semester & tahun ajaran tertentu.
     *
     * Rumus:
     *   NA = (rata_tugas * weight_tugas/100)
     *      + (rata_ph * weight_ph/100)
     *      + (uts * weight_uts/100)
     *      + (uas * weight_uas/100)
     *
     * @return array{components: array<string, array<string, mixed>>, total_weight: int, final_grade: float|null, passed: bool|null}|null
     *                                                                                                                                    null jika bobot komponen belum dikonfigurasi untuk mapel/semester/TA ini.
     */
    public function calculateSubjectGrade(int $studentId, int $subjectId, string $semester, string $academicYear): ?array
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

        return $this->computeGrade($scores, $components);
    }

    /**
     * Hitung NA untuk banyak kombinasi siswa x mapel dalam 2 query saja.
     *
     * @param  array<int, int>  $studentIds
     * @param  array<int, int>  $subjectIds
     * @return array<int, array<int, array<string, mixed>|null>>
     */
    public function calculateGrades(array $studentIds, array $subjectIds, string $semester, string $academicYear): array
    {
        $studentIds = array_values(array_unique(array_map('intval', $studentIds)));
        $subjectIds = array_values(array_unique(array_map('intval', $subjectIds)));

        if (empty($studentIds) || empty($subjectIds)) {
            return [];
        }

        $scores = Score::whereIn('student_id', $studentIds)
            ->whereIn('subject_id', $subjectIds)
            ->where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->get(['student_id', 'subject_id', 'component_code', 'value'])
            ->groupBy(fn (Score $score) => $score->student_id.'.'.$score->subject_id);

        $componentsBySubject = ScoreComponent::whereIn('subject_id', $subjectIds)
            ->where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->get(['subject_id', 'code', 'name', 'weight'])
            ->groupBy('subject_id')
            ->map(fn (Collection $items) => $items->keyBy('code'));

        $results = [];

        foreach ($studentIds as $studentId) {
            foreach ($subjectIds as $subjectId) {
                $components = $componentsBySubject->get($subjectId, collect());

                $results[$studentId][$subjectId] = $components->isEmpty()
                    ? null
                    : $this->computeGrade($scores->get($studentId.'.'.$subjectId, collect()), $components);
            }
        }

        return $results;
    }

    /**
     * Rakit laporan rapor lengkap untuk satu siswa pada semester & TA tertentu.
     *
     * Hanya mapel yang dipelajari oleh kelas siswa (via teacher_subjects) yang
     * diikutkan, diurutkan alfabetis.
     *
     * @return array{subjects: array<int, array<string, mixed>>, overall_average: float|null, passed_all: bool|null}
     */
    public function generate(Student $student, string $semester, string $academicYear): array
    {
        $subjects = Subject::whereHas('teacherSubjects', fn ($q) => $q->where('classroom_id', $student->classroom_id)
        )->orderBy('name')->get();

        $grades = $this->calculateGrades([$student->id], $subjects->pluck('id')->all(), $semester, $academicYear);

        $subjectReports = [];
        $totalGrade = 0;
        $subjectCount = 0;

        foreach ($subjects as $subject) {
            $result = $grades[$student->id][$subject->id] ?? null;

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

        return [
            'subjects' => $subjectReports,
            'overall_average' => $subjectCount > 0 ? round($totalGrade / $subjectCount, 2) : null,
            'passed_all' => $subjectCount > 0
                ? collect($subjectReports)->every(fn ($s) => $s['passed'] !== false)
                : null,
        ];
    }

    /**
     * Hitung rincian NA dari data nilai & komponen yang sudah dimuat ke memori.
     *
     * @param  Collection<int, Score>  $scores
     * @param  Collection<string, ScoreComponent>  $components  di-keyBy code
     */
    private function computeGrade(Collection $scores, Collection $components): ?array
    {
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
                'average_score' => $avgScore !== null ? round($avgScore, 2) : null,
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
}
