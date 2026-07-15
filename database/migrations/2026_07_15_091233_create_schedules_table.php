<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_subject_id')->constrained()->cascadeOnDelete();
            $table->enum('day', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']);
            $table->time('start_time'); // e.g. "14:00"
            $table->time('end_time');   // e.g. "14:50"
            $table->integer('hour_order'); // urutan jam ke-1, ke-2, dst
            $table->timestamps();

            $table->unique(['teacher_subject_id', 'day', 'hour_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
