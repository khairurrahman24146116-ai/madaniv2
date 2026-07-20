<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model ScoreComponent — Bobot komponen penilaian per mata pelajaran.
 *
 * FR-3.2: Admin mengonfigurasi persentase bobot untuk Tugas, PH, UTS, UAS.
 * Bobot digunakan untuk kalkulasi Nilai Akhir (NA) otomatis.
 */
#[Fillable(['subject_id', 'code', 'name', 'weight', 'semester', 'academic_year'])]
class ScoreComponent extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * Relasi ke mata pelajaran.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
