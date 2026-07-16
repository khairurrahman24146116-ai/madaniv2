<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Model Subject — Daftar mata pelajaran SMA.
 *
 * Fase 1 (FR-1.1): Setiap mata pelajaran bisa diajarkan oleh satu atau lebih guru
 * di kelas yang berbeda melalui tabel pivot teacher_subjects.
 * Kode mata pelajaran (code) bersifat unik untuk identifikasi cepat.
 */
#[Fillable(['name', 'code', 'description'])]
class Subject extends Model
{
    use HasFactory;

    /**
     * Relasi ke mapping guru-mata pelajaran untuk subjek ini.
     */
    public function teacherSubjects(): HasMany
    {
        return $this->hasMany(TeacherSubject::class);
    }

    /**
     * Relasi tembus ke semua guru yang mengajar subjek ini.
     */
    public function teachers(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, TeacherSubject::class, 'subject_id', 'id', 'id', 'user_id');
    }
}
