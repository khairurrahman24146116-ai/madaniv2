<?php

namespace App\Http\Controllers;

use App\Models\ActiveLetterRequest;
use App\Models\Student;
use App\Services\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ActiveLetterController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $letters = ActiveLetterRequest::with(['student.classroom', 'teacher', 'approver', 'taker'])
            ->when($user->isWaliMurid(), fn ($q) => $q->whereHas('student', fn ($q) => $q->where('user_id', $user->id)))
            ->orderByRaw("FIELD(status, 'progres', 'selesai', 'diambil', 'ditolak')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('active-letters.index', compact('letters'));
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $students = Student::with('classroom')
            ->where('is_active', true)
            ->when($user->isWaliMurid(), fn ($q) => $q->where('user_id', $user->id))
            ->orderBy('name')
            ->get();

        return view('active-letters.create', compact('students'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'purpose' => 'required|max:500',
        ]);

        $user = $request->user();

        if ($user->isWaliMurid()) {
            $student = Student::where('id', $validated['student_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();
        } else {
            $student = Student::findOrFail($validated['student_id']);
        }

        $sppPaid = $student->isFeePaid(now()->month, now()->year);

        $data = [
            'student_id' => $validated['student_id'],
            'teacher_id' => $user->id,
            'purpose' => $validated['purpose'],
            'spp_verified' => $sppPaid,
        ];

        if ($sppPaid) {
            $romanMonth = match (now()->month) {
                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
            };
            $data['status'] = 'selesai';
            $data['approved_by'] = $user->id;
            $data['letter_number'] = '421.3/SMA-MA/ST-AKTIF/'.$romanMonth.'/'.now()->year;
        } else {
            $data['status'] = 'ditolak';
            $data['rejected_reason'] = 'SPP bulan ini belum lunas. Silakan hubungi pihak sekolah untuk informasi lebih lanjut.';
        }

        $letter = ActiveLetterRequest::create($data);

        ActivityLogger::log('create', 'Mengajukan surat aktif untuk siswa: '.$student->name);

        if ($sppPaid) {
            return redirect()->route('active-letters.index')
                ->with('success', 'Surat keterangan aktif berhasil diterbitkan. Nomor: '.$data['letter_number']);
        }

        return redirect()->route('active-letters.index')
            ->with('error', 'Pengajuan ditolak: SPP bulan ini belum lunas. Silakan hubungi pihak sekolah.');
    }

    public function show(ActiveLetterRequest $activeLetter)
    {
        $activeLetter->load(['student.classroom', 'teacher', 'approver', 'taker']);

        return view('active-letters.show', compact('activeLetter'));
    }

    public function markTaken(Request $request, ActiveLetterRequest $activeLetter): RedirectResponse
    {
        if ($activeLetter->status !== 'selesai') {
            return redirect()->back()->with('error', 'Hanya surat dengan status selesai yang bisa diambil');
        }

        $activeLetter->update([
            'status' => 'diambil',
            'taken_by' => $request->user()->id,
            'taken_at' => now(),
        ]);

        ActivityLogger::log('update', 'Menandai surat aktif diambil: '.$activeLetter->student->name);

        return redirect()->route('active-letters.index')->with('success', 'Surat aktif ditandai sudah diambil oleh siswa');
    }

    public function printPdf(ActiveLetterRequest $activeLetter)
    {
        if (! in_array($activeLetter->status, ['selesai', 'diambil'])) {
            return redirect()->back()->with('error', 'Surat hanya bisa dicetak saat status selesai atau diambil');
        }

        $activeLetter->load(['student.classroom', 'teacher', 'approver']);

        $pdf = Pdf::loadView('pdf.active-letter', [
            'letter' => $activeLetter,
            'school' => 'SMA Dayah Madani Al-Aziziyah',
        ]);

        $filename = 'surat_aktif_'.str_replace(' ', '_', $activeLetter->student->name).'.pdf';

        return $pdf->download($filename);
    }
}
