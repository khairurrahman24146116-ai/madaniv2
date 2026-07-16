<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $nis = fake()->unique()->numerify('1###');

        return [
            'user_id' => User::factory()->create(['role' => 'wali_murid', 'password' => Hash::make('siswa123')])->id,
            'classroom_id' => Classroom::factory(),
            'nis' => $nis,
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['L', 'P']),
            'birth_date' => fake()->date('Y-m-d', '2010-01-01'),
            'is_active' => true,
        ];
    }
}
