<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['teacher_subject_id', 'day', 'start_time', 'end_time', 'hour_order'])]
class Schedule extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'hour_order' => 'integer',
        ];
    }

    public function teacherSubject(): BelongsTo
    {
        return $this->belongsTo(TeacherSubject::class);
    }


}
