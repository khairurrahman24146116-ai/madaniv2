<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model PaymentReceipt — Ledger pembayaran SPP (append-only).
 *
 * Setiap baris adalah kwitansi (pemasukan) atau reversal (koreksi,
 * ditandai kolom reversal_of). Kwitansi tidak boleh di-update/di-delete;
 * koreksi harus dibuat entri reversal baru agar jejak tetap lengkap.
 */
#[Fillable(['receipt_number', 'student_id', 'month', 'year', 'amount', 'method', 'reference', 'proof_path', 'note', 'recorded_by', 'reversal_of'])]
class PaymentReceipt extends Model
{
    use HasFactory;
    use LogsActivity;

    public const METHODS = ['cash', 'transfer', 'virtual_account', 'qris'];

    public const METHODS_LABELS = [
        'cash' => 'Tunai',
        'transfer' => 'Transfer Bank',
        'virtual_account' => 'Virtual Account',
        'qris' => 'QRIS / E-Wallet',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'month' => 'integer',
            'year' => 'integer',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Kwitansi asli yang dibatalkan oleh entri reversal ini.
     */
    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversal_of');
    }

    /**
     * Kwitansi asli yang punya entri reversal.
     */
    public function reversalEntry(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversal_of');
    }

    public function isReversal(): bool
    {
        return $this->reversal_of !== null;
    }

    public function methodLabel(): string
    {
        return self::METHODS_LABELS[$this->method] ?? ucfirst($this->method);
    }
}
