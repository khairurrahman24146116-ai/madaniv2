<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentIdorTest extends TestCase
{
    use RefreshDatabase;

    public function test_wali_murid_can_only_see_own_student_in_index(): void
    {
        $wali = User::factory()->create(['role' => 'wali_murid']);
        $own = Student::factory()->create(['user_id' => $wali->id]);
        Student::factory()->create(['user_id' => User::factory()->create(['role' => 'wali_murid'])->id]);

        Sanctum::actingAs($wali);

        $this->getJson('/students')
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.id', $own->id);
    }

    public function test_wali_cannot_view_another_student(): void
    {
        $wali = User::factory()->create(['role' => 'wali_murid']);
        $other = Student::factory()->create(['user_id' => User::factory()->create(['role' => 'wali_murid'])->id]);

        Sanctum::actingAs($wali);

        $this->getJson('/students/'.$other->id)
            ->assertForbidden();
    }

    public function test_wali_can_view_own_student(): void
    {
        $wali = User::factory()->create(['role' => 'wali_murid']);
        $own = Student::factory()->create(['user_id' => $wali->id]);

        Sanctum::actingAs($wali);

        $this->getJson('/students/'.$own->id)
            ->assertOk();
    }

    public function test_guru_cannot_access_student_outside_taught_classroom(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $taught = Classroom::factory()->create();
        $otherClassroom = Classroom::factory()->create();
        TeacherSubject::factory()->create(['user_id' => $guru->id, 'classroom_id' => $taught->id]);

        $other = Student::factory()->create(['classroom_id' => $otherClassroom->id]);

        Sanctum::actingAs($guru);

        $this->getJson('/students/'.$other->id)
            ->assertForbidden();
    }

    public function test_guru_can_access_student_in_taught_classroom(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $taught = Classroom::factory()->create();
        TeacherSubject::factory()->create(['user_id' => $guru->id, 'classroom_id' => $taught->id]);

        $student = Student::factory()->create(['classroom_id' => $taught->id]);

        Sanctum::actingAs($guru);

        $this->getJson('/students/'.$student->id)
            ->assertOk();
    }

    public function test_admin_can_access_any_student(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = Student::factory()->create();

        Sanctum::actingAs($admin);

        $this->getJson('/students/'.$student->id)
            ->assertOk();
    }
}
