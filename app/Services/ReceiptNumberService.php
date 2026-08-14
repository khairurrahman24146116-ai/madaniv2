<?php

namespace App\Services;

use App\Models\FinanceSequence;
use Illuminate\Support\Facades\DB;

class ReceiptNumberService
{
    /**
     * Bikin nomor kwitansi berurut tanpa celah/duplikat untuk satu tahun.
     *
     * Menggunakan baris FinanceSequence yang dikunci (lockForUpdate) di dalam
     * transaksi, sehingga dua pencatatan bersamaan tidak akan mendapat nomor
     * yang sama. Format: INV/{tahun}/{nomor 6 digit}.
     */
    public static function next(int $year): string
    {
        return DB::transaction(function () use ($year) {
            FinanceSequence::firstOrCreate(['year' => $year]);

            $sequence = FinanceSequence::where('year', $year)->lockForUpdate()->firstOrFail();
            $sequence->increment('last_number');

            return sprintf('INV/%d/%06d', $year, $sequence->last_number);
        });
    }
}
