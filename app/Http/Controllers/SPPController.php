<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentReceiptRequest;
use App\Models\PaymentReceipt;
use App\Models\Student;
use App\Models\StudentFee;
use App\Services\ActivityLogger;
use App\Services\ReceiptNumberService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SPPController extends Controller
{
    public function payer(Request $request): View
    {
        $user = $request->user();
        $student = $user->isWaliMurid()
            ? Student::where('user_id', $user->id)->with(['classroom', 'studentFees'])->first()
            : Student::with(['classroom', 'studentFees'])->first();

        return view('spp.payer', compact('student'));
    }

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

    /**
     * Mencatat pembayaran SPP — hanya bendahara.
     * Membuat kesit/kwitansi (PaymentReceipt) dengan nomor berurut
     * lalu menyinkronkan ringkasan StudentFee. Ledger bersifat append-only.
     */
    public function markPaid(StorePaymentReceiptRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($validated, $request) {
            /** @var Student $student */
            $student = Student::with('studentFees')->findOrFail($validated['student_id']);
            $month = (int) $validated['month'];
            $year = (int) $validated['year'];

            $fee = $student->studentFees()
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            if ($fee && $fee->is_paid) {
                return redirect()->route('spp.index', ['month' => $month, 'year' => $year])
                    ->with('error', 'SPP '.$student->name.' bulan ini sudah lunas.');
            }

            $proofPath = $request->file('proof')->store('receipts', 'public');

            $receipt = PaymentReceipt::create([
                'receipt_number' => ReceiptNumberService::next($year),
                'student_id' => $student->id,
                'month' => $month,
                'year' => $year,
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'reference' => $validated['reference'] ?? null,
                'proof_path' => $proofPath,
                'note' => $validated['note'] ?? null,
                'recorded_by' => $request->user()->id,
            ]);

            if ($fee) {
                $fee->update([
                    'is_paid' => true,
                    'paid_at' => now(),
                    'amount' => $validated['amount'],
                ]);
            } else {
                StudentFee::create([
                    'student_id' => $student->id,
                    'month' => $month,
                    'year' => $year,
                    'amount' => $validated['amount'],
                    'is_paid' => true,
                    'paid_at' => now(),
                ]);
            }

            ActivityLogger::log('create', 'Mencatat pembayaran SPP: '.$student->name.' bulan '.$month.'/'.$year.' (kwitansi '.$receipt->receipt_number.')', $receipt);

            return redirect()->route('spp.index', ['month' => $month, 'year' => $year])
                ->with('success', 'Kwitansi '.$receipt->receipt_number.' untuk '.$student->name.' berhasil dibuat.');
        });
    }

    /**
     * Membatalkan pembayaran SPP — hanya bendahara.
     * Tidak mengubah/menghapus kwitansi asli; malah membuat entri reversal
     * baru di ledger lalu mengembalikan status StudentFee menjadi belum lunas.
     */
    public function markUnpaid(Request $request, StudentFee $studentFee): RedirectResponse
    {
        if (! $request->user()->isBendahara()) {
            abort(403, 'Hanya bendahara yang dapat membatalkan pembayaran SPP.');
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        return DB::transaction(function () use ($request, $studentFee, $validated) {
            if (! $studentFee->is_paid) {
                return redirect()->route('spp.index', ['month' => $studentFee->month, 'year' => $studentFee->year])
                    ->with('error', 'SPP bulan ini belum lunas.');
            }

            $receipts = PaymentReceipt::where('student_id', $studentFee->student_id)
                ->where('month', $studentFee->month)
                ->where('year', $studentFee->year)
                ->whereNull('reversal_of')
                ->get();

            if ($receipts->isEmpty()) {
                return redirect()->route('spp.index', ['month' => $studentFee->month, 'year' => $studentFee->year])
                    ->with('error', 'Tidak ada kwitansi pembayaran yang bisa dibatalkan.');
            }

            foreach ($receipts as $receipt) {
                PaymentReceipt::create([
                    'receipt_number' => ReceiptNumberService::next((int) $studentFee->year),
                    'student_id' => $receipt->student_id,
                    'month' => $receipt->month,
                    'year' => $receipt->year,
                    'amount' => $receipt->amount,
                    'method' => $receipt->method,
                    'reference' => $receipt->reference,
                    'note' => 'Pembatalan kwitansi '.$receipt->receipt_number.'. Alasan: '.($validated['reason'] ?? '-'),
                    'recorded_by' => $request->user()->id,
                    'reversal_of' => $receipt->id,
                ]);
            }

            $studentFee->update([
                'is_paid' => false,
                'paid_at' => null,
            ]);

            ActivityLogger::log('update', 'Membatalkan pembayaran SPP: '.$studentFee->student->name.' bulan '.$studentFee->month.'/'.$studentFee->year);

            return redirect()->route('spp.index', ['month' => $studentFee->month, 'year' => $studentFee->year])
                ->with('success', 'Pembayaran SPP dibatalkan. Kwitansi asli tetap tersimpan, dibuat entri reversal.');
        });
    }
}
