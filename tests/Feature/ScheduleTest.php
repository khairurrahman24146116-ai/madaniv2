<?php

namespace Tests\Feature;

use App\Models\TeacherSubject;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    private string $adminToken;

    private string $guruToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@madani.id')->first();
        $this->adminToken = $admin->createToken('test')->plainTextToken;
        $guru = User::where('email', 'ahmad@madani.id')->first();
        $this->guruToken = $guru->createToken('test')->plainTextToken;
    }

    public function test_index(): void
    {
        $response = $this->withToken($this->adminToken)->getJson('/schedules');
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_store(): void
    {
        $ts = TeacherSubject::first();
        $response = $this->withToken($this->adminToken)->postJson('/schedules', [
            'teacher_subject_id' => $ts->id,
            'day' => 'senin',
            'start_time' => '14:00',
            'end_time' => '14:50',
            'hour_order' => 2,
        ]);
        $response->assertStatus(201)->assertJsonPath('success', true);
    }

    public function test_store_unauthorized_for_guru(): void
    {
        $response = $this->withToken($this->guruToken)->postJson('/schedules', [
            'teacher_subject_id' => 1,
            'day' => 'senin',
            'start_time' => '14:00',
            'end_time' => '14:50',
            'hour_order' => 3,
        ]);
        $response->assertStatus(403);
    }

    public function test_show(): void
    {
        $response = $this->withToken($this->adminToken)->getJson('/schedules/1');
        $response->assertStatus(200);
    }

    public function test_update(): void
    {
        $response = $this->withToken($this->adminToken)->putJson('/schedules/1', [
            'start_time' => '15:00',
            'end_time' => '15:50',
        ]);
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_destroy(): void
    {
        $response = $this->withToken($this->adminToken)->deleteJson('/schedules/1');
        $response->assertStatus(200)->assertJsonPath('success', true);
    }
}
