<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_success(): void
    {
        $response = $this->postJson('/auth/login', [
            'email' => 'admin@madani.id',
            'password' => 'admin123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_login_invalid_credentials(): void
    {
        $response = $this->postJson('/auth/login', [
            'email' => 'admin@madani.id',
            'password' => 'wrong',
        ]);

        $response->assertStatus(422);
    }

    public function test_me_authenticated(): void
    {
        $user = User::where('email', 'admin@madani.id')->first();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_me_unauthenticated(): void
    {
        $response = $this->getJson('/auth/me');
        $response->assertStatus(401);
    }
}
