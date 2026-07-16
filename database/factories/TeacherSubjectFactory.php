<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherSubjectFactory extends Factory
{
    protected $model = TeacherSubject::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(['role' => 'guru'])->id,
            'subject_id' => Subject::factory(),
            'classroom_id' => Classroom::factory(),
        ];
    }
}
