<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel scores — Nilai siswa per komponen penilaian.
     *
     * FR-3.1: Guru menginput nilai Tugas, PH (Kuis), UTS, dan UAS.
     * Satu siswa bisa memiliki banyak nilai untuk komponen tugas/ph (multiple entries),
     * dan satu nilai untuk uts/uas. Nilai akhir dikalkulasi otomatis (FR-3.2).
     */
    public function up(): void
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('component_code', 10); // tugas, ph, uts, uas
            $table->decimal('value', 5, 2); // 0 - 100
            $table->string('description')->nullable(); // "Tugas 1", "PH 2", "UTS Genap"
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('semester', ['ganjil', 'genap']);
            $table->string('academic_year', 9);
            $table->timestamps();

            $table->index(['student_id', 'subject_id', 'semester', 'academic_year'], 'scores_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
