<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'description'])]
class Subject extends Model
{
    use HasFactory;

    public function teacherSubjects(): HasMany
    {
        return $this->hasMany(TeacherSubject::class);
    }

    public function teachers(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, TeacherSubject::class, 'subject_id', 'id', 'id', 'user_id');
    }
}
