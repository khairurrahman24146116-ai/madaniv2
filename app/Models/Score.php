<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Score — Nilai siswa per komponen penilaian.
 *
 * FR-3.1: Menyimpan nilai Tugas, PH (Kuis), UTS, UAS per siswa per mata pelajaran.
 * FR-3.2: Nilai akhir (NA) dikalkulasi otomatis dari nilai terbobot.
 */
#[Fillable(['student_id', 'subject_id', 'component_code', 'value', 'description', 'teacher_id', 'semester', 'academic_year'])]
class Score extends Model
{
    use HasFactory;
    use LogsActivity;

    protected function casts(): array
    {
        return [
            'value' => 'float',
        ];
    }

    /**
     * Relasi ke siswa.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relasi ke mata pelajaran.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relasi ke guru yang menginput nilai.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
