<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassroomTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('email', 'admin@madani.id')->first();
        $this->token = $this->admin->createToken('test')->plainTextToken;
    }

    public function test_index(): void
    {
        $response = $this->withToken($this->token)->getJson('/classrooms');
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_store(): void
    {
        $response = $this->withToken($this->token)->postJson('/classrooms', [
            'name' => 'X IPA 2',
            'grade' => 'X',
            'academic_year' => '2025/2026',
        ]);
        $response->assertStatus(201)->assertJsonPath('success', true);
    }

    public function test_store_unauthorized_for_guru(): void
    {
        $guru = User::where('email', 'ahmad@madani.id')->first();
        $token = $guru->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson('/classrooms', [
            'name' => 'X IPA 3',
            'grade' => 'X',
            'academic_year' => '2025/2026',
        ]);
        $response->assertStatus(403);
    }

    public function test_show(): void
    {
        $response = $this->withToken($this->token)->getJson('/classrooms/1');
        $response->assertStatus(200);
    }

    public function test_update(): void
    {
        $response = $this->withToken($this->token)->putJson('/classrooms/1', [
            'name' => 'X IPA 1 Updated',
        ]);
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_destroy_with_students_fails(): void
    {
        $response = $this->withToken($this->token)->deleteJson('/classrooms/1');
        $response->assertStatus(422);
    }
}
