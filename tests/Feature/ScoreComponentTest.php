<?php

namespace Tests\Feature;

use App\Models\ScoreComponent;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoreComponentTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    private string $guruToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@madani.id')->first();
        $this->token = $admin->createToken('test')->plainTextToken;
        $guru = User::where('email', 'ahmad@madani.id')->first();
        $this->guruToken = $guru->createToken('test')->plainTextToken;
    }

    public function test_index(): void
    {
        $response = $this->withToken($this->token)->getJson('/score-components');
        $response->assertStatus(200)->assertJsonPath('success', true);
        $response->assertJsonCount(32, 'data');
    }

    public function test_index_filtered_by_subject(): void
    {
        $subject = Subject::first();
        $response = $this->withToken($this->token)->getJson("/score-components?subject_id={$subject->id}");
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_store(): void
    {
        $subject = Subject::factory()->create();

        $response = $this->withToken($this->token)->postJson('/score-components', [
            'subject_id' => $subject->id,
            'code' => 'tugas',
            'name' => 'Tugas',
            'weight' => 20,
            'semester' => 'ganjil',
            'academic_year' => '2025/2026',
        ]);
        $response->assertStatus(201);
    }

    public function test_store_unauthorized_for_guru(): void
    {
        $response = $this->withToken($this->guruToken)->postJson('/score-components', [
            'subject_id' => 1,
            'code' => 'tugas',
            'name' => 'Tugas',
            'weight' => 20,
            'semester' => 'ganjil',
            'academic_year' => '2025/2026',
        ]);
        $response->assertStatus(403);
    }

    public function test_weight_exceeds_100_fails(): void
    {
        $subject = Subject::first();

        $response = $this->withToken($this->token)->postJson('/score-components', [
            'subject_id' => $subject->id,
            'code' => 'tugas',
            'name' => 'Tugas',
            'weight' => 100,
            'semester' => 'ganjil',
            'academic_year' => '2025/2026',
        ]);
        $response->assertStatus(422);
    }

    public function test_show(): void
    {
        $component = ScoreComponent::first();

        $response = $this->withToken($this->token)->getJson("/score-components/{$component->id}");
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_update(): void
    {
        $component = ScoreComponent::where('code', 'tugas')->first();

        $response = $this->withToken($this->token)->putJson("/score-components/{$component->id}", [
            'weight' => 15,
        ]);
        $response->assertStatus(200)->assertJsonPath('success', true);
        $this->assertEquals(15, $component->fresh()->weight);
    }

    public function test_destroy(): void
    {
        $subject = Subject::factory()->create();
        $component = ScoreComponent::factory()->create([
            'subject_id' => $subject->id,
        ]);

        $response = $this->withToken($this->token)->deleteJson("/score-components/{$component->id}");
        $response->assertStatus(200)->assertJsonPath('success', true);
    }
}
