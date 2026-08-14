<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Model User — Mewakili semua pengguna sistem (Admin, Guru, Wali Murid).
 *
 * Fase 1 (FR-1.1): Guru dimapping ke mata pelajaran & kelas melalui relasi teacherSubjects.
 * Fase 1 (FR-1.2): Wali murid (orang tua) terhubung ke data siswa melalui relasi students.
 *
 * Role-Based Access Control (RBAC) sederhana menggunakan kolom 'role'.
 */
#[Fillable(['name', 'email', 'password', 'role', 'phone', 'address', 'is_active', 'profile_photo_path', 'must_change_password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo_path
            ? asset('storage/'.$this->profile_photo_path)
            : '';
    }

    /**
     * Relasi ke data siswa (untuk role wali_murid).
     * Satu user (orang tua) bisa memiliki lebih dari satu anak di sekolah.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Relasi ke mapping guru-mata pelajaran (untuk role guru).
     * Satu guru bisa mengajar banyak mata pelajaran di banyak kelas.
     */
    public function teacherSubjects(): HasMany
    {
        return $this->hasMany(TeacherSubject::class, 'user_id');
    }

    /**
     * Relasi tembus ke kelas yang diajar oleh guru ini.
     * Melalui tabel pivot teacher_subjects.
     */
    public function taughtClassrooms(): HasManyThrough
    {
        return $this->hasManyThrough(Classroom::class, TeacherSubject::class, 'user_id', 'id', 'id', 'classroom_id');
    }

    /**
     * Relasi ke absensi guru.
     */
    public function teacherAttendances(): HasMany
    {
        return $this->hasMany(TeacherAttendance::class, 'user_id');
    }

    /**
     * Relasi ke surat yang dibuat oleh pengguna.
     */
    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class, 'user_id');
    }

    /**
     * Relasi ke pesan masuk (contact messages) dari pengguna.
     */
    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class, 'user_id');
    }

    /**
     * Relasi ke permintaan pertemuan (meetings) dari pengguna.
     */
    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'user_id');
    }

    /**
     * Cek apakah user memiliki role admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user memiliki role guru.
     */
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    /**
     * Cek apakah user memiliki role wali murid.
     */
    public function isWaliMurid(): bool
    {
        return $this->role === 'wali_murid';
    }

    /**
     * Cek apakah user memiliki role bendahara (petugas keuangan).
     */
    public function isBendahara(): bool
    {
        return $this->role === 'bendahara';
    }
}
