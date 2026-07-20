<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model TeacherSubject (Pivot) — Mapping Guru-Mata Pelajaran-Kelas.
 *
 * Fase 1 (FR-1.1): Menghubungkan siapa (user_id) mengajar apa (subject_id)
 * di kelas mana (classroom_id). Kombinasi ketiganya bersifat unik.
 * Satu guru bisa memiliki banyak mapping (mengajar banyak subjek/kelas).
 * Satu mapping bisa memiliki banyak jadwal (hari dan jam berbeda).
 */
#[Fillable(['user_id', 'subject_id', 'classroom_id'])]
class TeacherSubject extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * Relasi ke data guru (user dengan role guru).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke mata pelajaran yang diajar.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relasi ke kelas tempat mengajar.
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Relasi ke jadwal mengajar untuk mapping ini.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
