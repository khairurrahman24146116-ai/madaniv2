<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private string $guruToken;

    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $guru = User::where('email', 'ahmad@madani.id')->first();
        $this->guruToken = $guru->createToken('test')->plainTextToken;
        $admin = User::where('email', 'admin@madani.id')->first();
        $this->adminToken = $admin->createToken('test')->plainTextToken;
    }

    public function test_form(): void
    {
        $scheduleId = Schedule::first()->id;

        $response = $this->withToken($this->guruToken)
            ->getJson("/attendances/form?schedule_id={$scheduleId}&date=2025-08-15");

        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_store(): void
    {
        $scheduleId = Schedule::first()->id;
        $students = Student::where('classroom_id', 1)->pluck('id');

        $response = $this->withToken($this->guruToken)
            ->postJson('/attendances', [
                'schedule_id' => $scheduleId,
                'date' => '2025-09-01',
                'attendances' => $students->map(fn ($id) => [
                    'student_id' => $id,
                    'status' => 'H',
                ])->toArray(),
            ]);

        $response->assertStatus(201)->assertJsonPath('success', true);
    }

    public function test_index(): void
    {
        $response = $this->withToken($this->guruToken)->getJson('/attendances');
        $response->assertStatus(200);
    }

    public function test_guru_cannot_filter_index_by_student_outside_taught_class(): void
    {
        $guru = User::where('email', 'ahmad@madani.id')->firstOrFail();
        $taughtClassroomIds = $guru->teacherSubjects()->pluck('classroom_id')->unique();
        $awayStudent = Student::whereNotIn('classroom_id', $taughtClassroomIds)->firstOrFail();

        $this->withToken($this->guruToken)
            ->getJson('/attendances?student_id='.$awayStudent->id)
            ->assertForbidden();
    }

    public function test_show(): void
    {
        $attendance = Attendance::first();

        $response = $this->withToken($this->guruToken)->getJson("/attendances/{$attendance->id}");
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_update(): void
    {
        $attendance = Attendance::first();

        $response = $this->withToken($this->guruToken)->putJson("/attendances/{$attendance->id}", [
            'status' => 'S',
            'notes' => 'Sakit demam',
        ]);
        $response->assertStatus(200)->assertJsonPath('success', true);
        $this->assertEquals('S', $attendance->fresh()->status);
    }

    public function test_destroy(): void
    {
        $attendance = Attendance::first();

        $response = $this->withToken($this->guruToken)->deleteJson("/attendances/{$attendance->id}");
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_export_csv(): void
    {
        $response = $this->withToken($this->guruToken)->getJson('/attendances/export-csv');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }
}
