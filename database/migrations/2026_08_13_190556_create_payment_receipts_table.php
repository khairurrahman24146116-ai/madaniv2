<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ledger pembayaran SPP — append-only.
     * Koreksi pembayaran hanya boleh dilakukan lewat entri reversal
     * (lihat kolom reversal_of), bukan update/delete.
     */
    public function up(): void
    {
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('amount', 12, 2);
            $table->string('method');
            $table->string('reference')->nullable();
            $table->string('proof_path')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reversal_of')->nullable()->constrained('payment_receipts')->nullOnDelete();
            $table->timestamps();

            $table->index(['student_id', 'month', 'year']);
            $table->index('recorded_by');
            $table->index('reversal_of');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};
