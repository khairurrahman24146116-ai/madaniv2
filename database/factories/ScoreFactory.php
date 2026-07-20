<?php

namespace Database\Factories;

use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScoreFactory extends Factory
{
    protected $model = Score::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'subject_id' => Subject::factory(),
            'component_code' => fake()->randomElement(['tugas', 'ph', 'uts', 'uas']),
            'value' => fake()->randomFloat(2, 0, 100),
            'description' => fake()->optional()->word(),
            'teacher_id' => User::factory(),
            'semester' => fake()->randomElement(['ganjil', 'genap']),
            'academic_year' => '2025/2026',
        ];
    }
}
