<?php

namespace Database\Factories;

use App\Models\ScoreComponent;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScoreComponentFactory extends Factory
{
    protected $model = ScoreComponent::class;

    public function definition(): array
    {
        $code = fake()->randomElement(['tugas', 'ph', 'uts', 'uas']);
        $names = [
            'tugas' => 'Tugas',
            'ph' => 'Penilaian Harian',
            'uts' => 'UTS',
            'uas' => 'UAS',
        ];

        return [
            'subject_id' => Subject::factory(),
            'code' => $code,
            'name' => $names[$code],
            'weight' => fake()->randomFloat(2, 5, 50),
            'semester' => fake()->randomElement(['ganjil', 'genap']),
            'academic_year' => '2025/2026',
        ];
    }
}
