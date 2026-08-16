<?php

namespace App\Services;

/**
 * Helper Predikat — memetakan Nilai Akhir (0–100) menjadi huruf mutu.
 *
 * Skala default sekolah:
 *   A: 90–100, B: 80–89, C: 70–79, D: < 70.
 */
class PredikatHelper
{
    public static function dariNilai(float $nilai): string
    {
        return match (true) {
            $nilai >= 90 => 'A',
            $nilai >= 80 => 'B',
            $nilai >= 70 => 'C',
            default => 'D',
        };
    }
}
