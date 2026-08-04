<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentFee;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SPPController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $month = (int) ($request->query('month', now()->month));
        $year = (int) ($request->query('year', now()->year));

        $studentBase = Student::query()
            ->where('is_active', true)
            ->when($user->isWaliMurid(), fn ($q) => $q->where('user_id', $user->id));

        $totalStudents = (clone $studentBase)->count();
        $totalLunas = (clone $studentBase)
            ->whereHas('studentFees', fn ($q) => $q->where('month', $month)->where('year', $year)->where('is_paid', true))
            ->count();
        $totalBelum = $totalStudents - $totalLunas;

        $students = (clone $studentBase)
            ->with(['classroom', 'studentFees' => function ($q) use ($month, $year) {
                $q->where('month', $month)->where('year', $year);
            }])
            ->orderBy('name')
            ->paginate(50)
            ->withQueryString();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('spp.index', compact('students', 'month', 'year', 'months', 'totalLunas', 'totalBelum'));
    }

    public function markPaid(Request $request): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat mencatat pembayaran SPP.');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
            'amount' => 'required|numeric|min:0',
        ]);

        $student = Student::with('studentFees')->findOrFail($validated['student_id']);

        $fee = $student->studentFees()
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->first();

        if ($fee) {
            if ($fee->is_paid) {
                return redirect()->route('spp.index', ['month' => $validated['month'], 'year' => $validated['year']])
                    ->with('error', 'SPP '.$student->name.' bulan ini sudah lunas.');
            }

            $fee->update([
                'is_paid' => true,
                'paid_at' => now(),
                'amount' => $validated['amount'],
            ]);
        } else {
            StudentFee::create([
                'student_id' => $validated['student_id'],
                'month' => $validated['month'],
                'year' => $validated['year'],
                'amount' => $validated['amount'],
                'is_paid' => true,
                'paid_at' => now(),
            ]);
        }

        ActivityLogger::log('create', 'Mencatat pembayaran SPP: '.$student->name.' bulan '.$validated['month'].'/'.$validated['year']);

        return redirect()->route('spp.index', ['month' => $validated['month'], 'year' => $validated['year']])
            ->with('success', 'Pembayaran SPP '.$student->name.' berhasil dicatat.');
    }

    public function markUnpaid(Request $request, StudentFee $studentFee): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat membatalkan pembayaran SPP.');
        }

        $studentFee->update([
            'is_paid' => false,
            'paid_at' => null,
        ]);

        ActivityLogger::log('update', 'Membatalkan pembayaran SPP: '.$studentFee->student->name);

        return redirect()->route('spp.index', ['month' => $studentFee->month, 'year' => $studentFee->year])
            ->with('success', 'Pembayaran SPP dibatalkan.');
    }
}
