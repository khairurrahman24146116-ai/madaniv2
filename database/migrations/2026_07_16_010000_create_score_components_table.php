<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel score_components — Konfigurasi bobot komponen penilaian.
     *
     * FR-3.2: Admin menentukan persentase bobot tiap komponen (Tugas, PH, UTS, UAS)
     * per mata pelajaran, per semester. Bobot digunakan untuk kalkulasi Nilai Akhir.
     */
    public function up(): void
    {
        Schema::create('score_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->enum('code', ['tugas', 'ph', 'uts', 'uas']);
            $table->string('name'); // "Tugas", "Penilaian Harian", "UTS", "UAS"
            $table->decimal('weight', 5, 2); // persentase bobot, misal: 15.00 = 15%
            $table->enum('semester', ['ganjil', 'genap']);
            $table->string('academic_year', 9); // "2025/2026"
            $table->timestamps();

            $table->unique(['subject_id', 'code', 'semester', 'academic_year'], 'sc_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_components');
    }
};
