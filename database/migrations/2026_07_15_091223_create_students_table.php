<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->string('nis', 20)->unique(); // Nomor Induk Siswa
            $table->string('name');
            $table->enum('gender', ['L', 'P']);
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('parent_name')->nullable();
            $table->string('parent_phone', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['classroom_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
