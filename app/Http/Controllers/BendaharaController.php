<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\PaymentReceipt;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\StreamedResponse;

class BendaharaController extends Controller
{
    private const MONTHS = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function dashboard(Request $request): View
    {
        $month = now()->month;
        $year = now()->year;

        $todayIncome = PaymentReceipt::whereNull('reversal_of')->whereDate('created_at', now()->toDateString())->sum('amount');
        $todayPayments = PaymentReceipt::whereNull('reversal_of')->whereDate('created_at', now()->toDateString())->count();

        $monthIncome = PaymentReceipt::whereNull('reversal_of')->where('month', $month)->where('year', $year)->sum('amount');
        $monthPayments = PaymentReceipt::whereNull('reversal_of')->where('month', $month)->where('year', $year)->count();

        $totalStudents = Student::where('is_active', true)->count();
        $paidStudents = Student::where('is_active', true)
            ->whereHas('studentFees', fn ($q) => $q->where('month', $month)->where('year', $year)->where('is_paid', true))
            ->count();

        $perClass = Classroom::orderBy('name')->get()
            ->map(function (Classroom $classroom) use ($month, $year) {
                $total = Student::where('classroom_id', $classroom->id)->where('is_active', true)->count();
                $paid = Student::where('classroom_id', $classroom->id)->where('is_active', true)
                    ->whereHas('studentFees', fn ($q) => $q->where('month', $month)->where('year', $year)->where('is_paid', true))
                    ->count();

                return [
                    'name' => $classroom->name,
                    'total' => $total,
                    'paid' => $paid,
                ];
            })
            ->filter(fn ($row) => $row['total'] > 0)
            ->values();

        $recentReceipts = PaymentReceipt::with(['student.classroom', 'recordedBy'])
            ->latest()
            ->limit(10)
            ->get();

        $months = self::MONTHS;

        return view('bendahara.dashboard', compact(
            'todayIncome', 'todayPayments', 'monthIncome', 'monthPayments',
            'totalStudents', 'paidStudents', 'perClass', 'recentReceipts', 'months', 'month', 'year'
        ));
    }

    public function rekap(Request $request): View
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $receipts = PaymentReceipt::with(['student.classroom'])
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('receipt_number')
            ->get();

        $income = $receipts->whereNull('reversal_of');
        $reversed = $receipts->whereNotNull('reversal_of');

        $totalIncome = round((float) $income->sum('amount'), 2);
        $byMethod = $income->groupBy('method')->map(fn ($rows) => round((float) $rows->sum('amount'), 2))->sortDesc();

        $months = self::MONTHS;

        return view('bendahara.rekap', compact('receipts', 'income', 'reversed', 'totalIncome', 'byMethod', 'months', 'month', 'year'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $receipts = PaymentReceipt::with(['student.classroom'])
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('receipt_number')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="rekap-spp-'.$year.'-'.$month.'.csv"',
        ];

        $callback = function () use ($receipts) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['No Kwitansi', 'NIS', 'Nama Siswa', 'Kelas', 'Bulan', 'Tahun', 'Nominal', 'Metode', 'No. Bukti', 'Status', 'Waktu', 'Dicatat Oleh']);

            foreach ($receipts as $receipt) {
                fputcsv($file, [
                    $receipt->receipt_number,
                    $receipt->student->nis,
                    $receipt->student->name,
                    $receipt->student->classroom?->name ?? '',
                    self::MONTHS[$receipt->month] ?? $receipt->month,
                    $receipt->year,
                    number_format((float) $receipt->amount, 2, ',', '.'),
                    $receipt->methodLabel(),
                    $receipt->reference ?? '',
                    $receipt->isReversal() ? 'Batal' : 'Lunas',
                    $receipt->created_at->format('d/m/Y H:i'),
                    $receipt->recordedBy?->name ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
