<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTest extends TestCase
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
        $response = $this->withToken($this->adminToken)->getJson('/subjects');
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_store(): void
    {
        $response = $this->withToken($this->adminToken)->postJson('/subjects', [
            'name' => 'Kimia',
            'code' => 'KIM',
        ]);
        $response->assertStatus(201)->assertJsonPath('success', true);
    }

    public function test_store_unauthorized_for_guru(): void
    {
        $response = $this->withToken($this->guruToken)->postJson('/subjects', [
            'name' => 'Kimia',
            'code' => 'KIM',
        ]);
        $response->assertStatus(403);
    }

    public function test_show(): void
    {
        $response = $this->withToken($this->adminToken)->getJson('/subjects/1');
        $response->assertStatus(200);
    }

    public function test_update(): void
    {
        $response = $this->withToken($this->adminToken)->putJson('/subjects/1', [
            'name' => 'Matematika Wajib',
        ]);
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_destroy_with_teachers_fails(): void
    {
        $response = $this->withToken($this->adminToken)->deleteJson('/subjects/1');
        $response->assertStatus(422);
    }
}
