<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Model Classroom — Representasi kelas/rombel SMA.
 *
 * Fase 1 (FR-1.2): Kelas dibagi dalam 3 tingkatan: X, XI, XII.
 * Setiap kelas memiliki daftar siswa dan mapping guru-mata pelajaran.
 */
#[Fillable(['name', 'grade', 'academic_year', 'description', 'wali_kelas_id'])]
class Classroom extends Model
{
    use HasFactory;
    use LogsActivity;

    protected function casts(): array
    {
        return [];
    }

    /**
     * Relasi ke siswa yang terdaftar di kelas ini.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Relasi ke mapping guru-mata pelajaran di kelas ini.
     */
    public function teacherSubjects(): HasMany
    {
        return $this->hasMany(TeacherSubject::class);
    }

    /**
     * Relasi tembus ke semua guru yang mengajar di kelas ini.
     */
    public function teachers(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, TeacherSubject::class, 'classroom_id', 'id', 'id', 'user_id');
    }

    /**
     * Relasi ke wali kelas (User dengan role guru).
     */
    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    /**
     * Scope untuk mengambil siswa aktif saja.
     */
    public function activeStudents(): HasMany
    {
        return $this->students()->where('is_active', true);
    }
}
