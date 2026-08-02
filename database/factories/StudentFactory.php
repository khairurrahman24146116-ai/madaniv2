<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $nis = fake()->unique()->numerify('1###');

        return [
            'user_id' => User::factory()->create(['role' => 'wali_murid', 'password' => Str::random(10), 'must_change_password' => true])->id,
            'classroom_id' => Classroom::factory(),
            'nis' => $nis,
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['L', 'P']),
            'birth_date' => fake()->date('Y-m-d', '2010-01-01'),
            'is_active' => true,
        ];
    }
}
