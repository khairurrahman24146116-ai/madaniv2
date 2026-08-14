<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\RaporService;
use Illuminate\View\View;

class WaliMuridController extends Controller
{
    /**
     * Web (wali murid): dashboard berisi kartu profil siswa.
     */
    public function dashboard(): View
    {
        $students = Student::with('classroom')
            ->withCount([
                'attendances as hadir_count' => fn ($q) => $q->where('status', 'H'),
                'attendances as tidak_hadir_count' => fn ($q) => $q->whereNot('status', 'H'),
            ])
            ->where('user_id', auth()->id())
            ->get();

        return view('wali-murid.dashboard', compact('students'));
    }

    /**
     * Web (wali murid): rapor anak berdasarkan semester & tahun ajaran.
     */
    public function rapor(Student $student): View
    {
        if ($student->user_id !== auth()->id()) {
            abort(403);
        }

        $student->load('classroom');

        $semester = request('semester', 'ganjil');
        $academicYear = request('academic_year', '2025/2026');
        $rapor = app(RaporService::class)->generate($student, $semester, $academicYear);

        return view('wali-murid.rapor', compact('student', 'rapor', 'semester', 'academicYear'));
    }
}
