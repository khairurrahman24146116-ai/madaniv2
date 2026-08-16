<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\RaporService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Verifikasi keaslian rapor via kode unik (publik, tanpa login).
 *
 * Kode dihitung dari hash(student_id|semester|tahun_ajaran|rata2|app_key).
 * URL verifikasi menyertakan parameter student/semester/academic_year agar
 * hash bisa dihitung ulang; ketidakcocokan menandakan dokumen tidak asli.
 */
class RaporVerificationController extends Controller
{
    public function verifikasi(Request $request, string $kode): View
    {
        $studentId = (int) $request->query('student', 0);
        $semester = (string) $request->query('semester', '');
        $academicYear = (string) $request->query('academic_year', '');

        $valid = false;
        $student = null;
        $overallAverage = null;

        if ($studentId > 0 && in_array($semester, ['ganjil', 'genap'], true) && $academicYear !== '') {
            $student = Student::with('classroom')->find($studentId);

            if ($student) {
                $rapor = app(RaporService::class)->generate($student, $semester, $academicYear);
                $overallAverage = $rapor['overall_average'];

                $expected = strtoupper(hash('sha256', implode('|', [
                    $student->id,
                    $semester,
                    $academicYear,
                    $overallAverage ?? '',
                    config('app.key'),
                ])));

                $valid = strtoupper($kode) === substr($expected, 0, 16);
            }
        }

        return view('rapor.verifikasi', compact('valid', 'student', 'semester', 'academicYear', 'overallAverage'));
    }
}
