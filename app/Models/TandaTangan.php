<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model TandaTangan — gambar tanda tangan digital (PNG transparan).
 *
 * Dipakai untuk menempel TTD Kepala Sekolah / Wali Kelas di atas nama pada
 * PDF rapor dan surat. role kepala_sekolah tidak terikat user tertentu
 * (diunggah oleh admin); role wali_kelas terikat ke akun guru (user_id).
 */
#[Fillable(['user_id', 'role', 'file_path', 'is_active'])]
class TandaTangan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Akun guru pemilik TTD (untuk role wali_kelas).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Path absolut ke berkas TTD di disk publik.
     */
    public function getFullPathAttribute(): ?string
    {
        return $this->file_path ? storage_path('app/public/'.$this->file_path) : null;
    }
}
