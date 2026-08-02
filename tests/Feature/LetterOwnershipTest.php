<?php

namespace Tests\Feature;

use App\Models\ActiveLetterRequest;
use App\Models\Letter;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LetterOwnershipTest extends TestCase
{
    use RefreshDatabase;

    private function makeLetter(bool $published = true): Letter
    {
        return Letter::create([
            'title' => 'Edaran',
            'content' => 'isi',
            'type' => 'edaran',
            'user_id' => User::factory()->create(['role' => 'admin'])->id,
            'is_published' => $published,
        ]);
    }

    private function makeActiveLetter(Student $student, User $teacher): ActiveLetterRequest
    {
        return ActiveLetterRequest::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'purpose' => 'Beasiswa',
            'status' => 'progres',
        ]);
    }

    public function test_wali_cannot_view_unpublished_letter(): void
    {
        $wali = User::factory()->create(['role' => 'wali_murid', 'must_change_password' => false]);
        $draft = $this->makeLetter(false);

        Sanctum::actingAs($wali);

        $this->get('/app/wali-murid/surat/'.$draft->id)->assertForbidden();
    }

    public function test_wali_can_view_published_letter(): void
    {
        $wali = User::factory()->create(['role' => 'wali_murid', 'must_change_password' => false]);
        $published = $this->makeLetter(true);

        Sanctum::actingAs($wali);

        $this->get('/app/wali-murid/surat/'.$published->id)->assertOk();
    }

    public function test_guru_cannot_view_unpublished_letter(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'must_change_password' => false]);
        $draft = $this->makeLetter(false);

        Sanctum::actingAs($guru);

        $this->get('/app/guru/surat/'.$draft->id)->assertForbidden();
    }

    public function test_guru_can_view_published_letter(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'must_change_password' => false]);
        $published = $this->makeLetter(true);

        Sanctum::actingAs($guru);

        $this->get('/app/guru/surat/'.$published->id)->assertOk();
    }

    public function test_wali_cannot_see_active_letter_of_other_student(): void
    {
        $other = Student::factory()->create();
        $letter = $this->makeActiveLetter($other, User::factory()->create(['role' => 'guru']));
        $wali = User::factory()->create(['role' => 'wali_murid', 'must_change_password' => false]);

        Sanctum::actingAs($wali);

        $this->get('/app/active-letters/'.$letter->id)->assertForbidden();
    }

    public function test_wali_can_view_own_active_letter(): void
    {
        $wali = User::factory()->create(['role' => 'wali_murid', 'must_change_password' => false]);
        $student = Student::factory()->create(['user_id' => $wali->id]);
        $letter = $this->makeActiveLetter($student, User::factory()->create(['role' => 'guru']));

        $this->assertTrue(Gate::forUser($wali)->allows('view', $letter));
    }

    public function test_admin_can_view_any_letter(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $letter = $this->makeActiveLetter(Student::factory()->create(), User::factory()->create(['role' => 'guru']));

        $this->assertTrue(Gate::forUser($admin)->allows('view', $letter));
    }
}
