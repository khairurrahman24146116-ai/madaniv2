<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Attendance — Absensi siswa per jam pelajaran.
 *
 * Fase 2 (FR-2.2): Status absensi: H (Hadir), S (Sakit), I (Izin), A (Alpa).
 * Fase 2 (FR-2.4): submitted_at mencatat timestamp saat guru submit.
 * Setiap record unik berdasarkan kombinasi (student_id, schedule_id, date).
 */
#[Fillable(['student_id', 'schedule_id', 'date', 'status', 'submitted_at', 'notes'])]
class Attendance extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke siswa yang diabsensi.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relasi ke jadwal pelajaran saat absensi dilakukan.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}
