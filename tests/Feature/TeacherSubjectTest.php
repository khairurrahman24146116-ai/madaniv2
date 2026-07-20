<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherSubjectTest extends TestCase
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
        $response = $this->withToken($this->adminToken)->getJson('/teacher-subjects');
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_store(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $subject = Subject::factory()->create();
        $classroom = Classroom::factory()->create();

        $response = $this->withToken($this->adminToken)->postJson('/teacher-subjects', [
            'user_id' => $guru->id,
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
        ]);
        $response->assertStatus(201)->assertJsonPath('success', true);
    }

    public function test_store_unauthorized_for_guru(): void
    {
        $response = $this->withToken($this->guruToken)->postJson('/teacher-subjects', [
            'user_id' => 1,
            'subject_id' => 1,
            'classroom_id' => 1,
        ]);
        $response->assertStatus(403);
    }

    public function test_show(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $subject = Subject::factory()->create();
        $classroom = Classroom::factory()->create();

        $ts = TeacherSubject::create([
            'user_id' => $guru->id,
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
        ]);

        $response = $this->withToken($this->adminToken)->getJson("/teacher-subjects/{$ts->id}");
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_update(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $subject = Subject::factory()->create();
        $classroom = Classroom::factory()->create();
        $newClassroom = Classroom::factory()->create();

        $ts = TeacherSubject::create([
            'user_id' => $guru->id,
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
        ]);

        $response = $this->withToken($this->adminToken)->putJson("/teacher-subjects/{$ts->id}", [
            'user_id' => $guru->id,
            'subject_id' => $subject->id,
            'classroom_id' => $newClassroom->id,
        ]);
        $response->assertStatus(200)->assertJsonPath('success', true);
        $this->assertEquals($newClassroom->id, $ts->fresh()->classroom_id);
    }

    public function test_update_validates_guru_role(): void
    {
        $nonGuru = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $subject = Subject::factory()->create();
        $classroom = Classroom::factory()->create();

        $ts = TeacherSubject::create([
            'user_id' => $guru->id,
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
        ]);

        $response = $this->withToken($this->adminToken)->putJson("/teacher-subjects/{$ts->id}", [
            'user_id' => $nonGuru->id,
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
        ]);
        $response->assertStatus(422);
    }

    public function test_destroy(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $subject = Subject::factory()->create();
        $classroom = Classroom::factory()->create();

        $ts = TeacherSubject::create([
            'user_id' => $guru->id,
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
        ]);

        $response = $this->withToken($this->adminToken)->deleteJson("/teacher-subjects/{$ts->id}");
        $response->assertStatus(200)->assertJsonPath('success', true);
    }
}
