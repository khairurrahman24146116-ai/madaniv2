<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoreComponentTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@madani.id')->first();
        $this->token = $admin->createToken('test')->plainTextToken;
    }

    public function test_weight_exceeds_100_fails(): void
    {
        $this->withToken($this->token)->postJson('/score-components', [
            'subject_id' => 1,
            'code' => 'tugas',
            'name' => 'Tugas',
            'weight' => 100,
            'semester' => 'ganjil',
            'academic_year' => '2025/2026',
        ])->assertStatus(422);
    }

    public function test_store_score_component(): void
    {
        $this->withToken($this->token)->postJson('/score-components', [
            'subject_id' => 4,
            'code' => 'tugas',
            'name' => 'Tugas',
            'weight' => 20,
            'semester' => 'ganjil',
            'academic_year' => '2025/2026',
        ])->assertStatus(201);
    }
}
