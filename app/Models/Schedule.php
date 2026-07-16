<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Schedule — Jadwal mengajar guru di blok sore.
 *
 * Fase 1 (FR-1.3): Waktu dibatasi otomatis pada rentang 14:00 - 16:00 WIB.
 * Setiap jadwal terkait dengan satu mapping guru-mata pelajaran-kelas,
 * memiliki hari (senin-sabtu), jam mulai-selesai, dan urutan jam (1-4).
 * Kombinasi (teacher_subject_id, day, hour_order) bersifat unik.
 */
#[Fillable(['teacher_subject_id', 'day', 'start_time', 'end_time', 'hour_order'])]
class Schedule extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'hour_order' => 'integer',
        ];
    }

    /**
     * Relasi ke mapping guru-mata pelajaran-kelas.
     */
    public function teacherSubject(): BelongsTo
    {
        return $this->belongsTo(TeacherSubject::class);
    }

    /**
     * Relasi ke data absensi untuk jadwal ini.
     * Fase 2: Satu jadwal bisa memiliki banyak record absensi (per siswa).
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
