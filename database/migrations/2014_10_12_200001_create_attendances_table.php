<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel attendances — Menyimpan data absensi siswa per jam pelajaran.
     *
     * Fase 2 (FR-2.2): Status absensi: H (Hadir), S (Sakit), I (Izin), A (Alpa).
     * Fase 2 (FR-2.4): submitted_at mencatat kapan guru melakukan submit.
     *
     * Satu siswa hanya memiliki satu status absensi untuk (jadwal + tanggal) yang sama.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['H', 'S', 'I', 'A']); // Hadir, Sakit, Izin, Alpa
            $table->timestamp('submitted_at')->nullable(); // FR-2.4: rekam waktu submit
            $table->text('notes')->nullable();
            $table->timestamps();

            // Satu siswa hanya bisa punya 1 status absensi per jadwal per hari
            $table->unique(['student_id', 'schedule_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
