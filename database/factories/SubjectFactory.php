<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Matematika', 'Fisika', 'Bahasa Inggris', 'Bahasa Indonesia', 'Kimia']),
            'code' => fake()->unique()->regexify('[A-Z]{3}'),
        ];
    }
}
