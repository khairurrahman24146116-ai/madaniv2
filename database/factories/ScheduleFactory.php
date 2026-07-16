<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\TeacherSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        return [
            'teacher_subject_id' => TeacherSubject::factory(),
            'day' => fake()->randomElement(['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']),
            'start_time' => '14:00',
            'end_time' => '14:50',
            'hour_order' => fake()->numberBetween(1, 4),
        ];
    }
}
