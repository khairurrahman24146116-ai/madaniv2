<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Gambar tanda tangan digital (PNG transparan) untuk Kepala Sekolah dan
     * Wali Kelas. Diunggah sekali lewat halaman profil dan ditempelkan di
     * atas nama pada PDF surat/rapor. Satu role/user hanya boleh memiliki
     * satu tanda tangan aktif; yang lama dinonaktifkan saat upload baru.
     */
    public function up(): void
    {
        Schema::create('tanda_tangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('role', ['kepala_sekolah', 'wali_kelas']);
            $table->string('file_path');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('role');
            $table->index(['user_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tanda_tangans');
    }
};
