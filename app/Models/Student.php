<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Student — Data santri/siswa SMA formal.
 *
 * Fase 1 (FR-1.2): Setiap siswa terdaftar di satu kelas (X/XI/XII)
 * dan memiliki akun user terkait (dengan role wali_murid untuk orang tua).
 * NIS bersifat unik sebagai identitas akademik siswa.
 */
#[Fillable(['user_id', 'classroom_id', 'nis', 'name', 'gender', 'birth_date', 'address', 'phone', 'parent_name', 'parent_phone', 'is_active'])]
class Student extends Model
{
    use HasFactory;
    use LogsActivity;

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke akun user (role wali_murid) untuk autentikasi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke kelas tempat siswa terdaftar.
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Relasi ke data absensi siswa ini.
     * Fase 2: Satu siswa memiliki banyak record absensi.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Relasi ke nilai siswa ini.
     */
    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }
}
