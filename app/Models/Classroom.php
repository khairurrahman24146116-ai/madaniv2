<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'grade', 'academic_year', 'description'])]
class Classroom extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [];
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teacherSubjects(): HasMany
    {
        return $this->hasMany(TeacherSubject::class);
    }

    public function teachers(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, TeacherSubject::class, 'classroom_id', 'id', 'id', 'user_id');
    }

    public function activeStudents(): HasMany
    {
        return $this->students()->where('is_active', true);
    }
}
