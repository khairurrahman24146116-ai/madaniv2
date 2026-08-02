<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['student_id', 'teacher_id', 'type', 'purpose', 'status', 'spp_verified', 'approved_by', 'rejected_reason', 'taken_by', 'taken_at', 'letter_number'])]
class ActiveLetterRequest extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'student_letter_requests';

    protected function casts(): array
    {
        return [
            'spp_verified' => 'boolean',
            'taken_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function taker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'taken_by');
    }
}
