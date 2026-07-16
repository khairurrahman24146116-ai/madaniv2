<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
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
        $response = $this->withToken($this->adminToken)->getJson('/students');
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_store(): void
    {
        $classroom = Classroom::first();
        $response = $this->withToken($this->adminToken)->postJson('/students', [
            'classroom_id' => $classroom->id,
            'nis' => '9999',
            'name' => 'Test Siswa',
            'gender' => 'L',
        ]);
        $response->assertStatus(201)->assertJsonPath('success', true);
    }

    public function test_store_unauthorized_for_guru(): void
    {
        $classroom = Classroom::first();
        $response = $this->withToken($this->guruToken)->postJson('/students', [
            'classroom_id' => $classroom->id,
            'nis' => '9998',
            'name' => 'Test Siswa',
            'gender' => 'L',
        ]);
        $response->assertStatus(403);
    }

    public function test_show(): void
    {
        $response = $this->withToken($this->adminToken)->getJson('/students/1');
        $response->assertStatus(200);
    }

    public function test_update(): void
    {
        $response = $this->withToken($this->adminToken)->putJson('/students/1', [
            'name' => 'Updated Name',
        ]);
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_destroy(): void
    {
        $classroom = Classroom::first();
        $user = User::factory()->create(['role' => 'wali_murid']);
        $student = Student::factory()->create(['classroom_id' => $classroom->id, 'user_id' => $user->id]);

        $response = $this->withToken($this->adminToken)->deleteJson("/students/{$student->id}");
        $response->assertStatus(200)->assertJsonPath('success', true);
    }
}
