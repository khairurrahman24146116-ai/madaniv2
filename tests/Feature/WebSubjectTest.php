<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebSubjectTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('email', 'admin@madani.id')->first();
    }

    public function test_admin_can_view_subject_pages(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.subjects.index'))
            ->assertOk();

        $this->actingAs($this->admin)
            ->get(route('admin.subjects.create'))
            ->assertOk();

        $this->actingAs($this->admin)
            ->get(route('admin.subjects.edit', Subject::first()))
            ->assertOk();
    }

    public function test_guru_cannot_access_admin_subject_pages(): void
    {
        $guru = User::where('email', 'ahmad@madani.id')->first();

        $this->actingAs($guru)
            ->get(route('admin.subjects.index'))
            ->assertForbidden();
    }

    public function test_store_creates_subject_and_redirects(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.subjects.store'), [
            'name' => 'Kimia',
            'code' => 'KIM',
        ]);

        $response->assertRedirect(route('admin.subjects.index'))
            ->assertSessionHas('success', 'Mapel berhasil ditambahkan');

        $this->assertDatabaseHas('subjects', ['name' => 'Kimia', 'code' => 'KIM']);
    }

    public function test_update_subject_and_redirects(): void
    {
        $subject = Subject::first();

        $response = $this->actingAs($this->admin)->put(route('admin.subjects.update', $subject), [
            'name' => 'Matematika Wajib',
            'code' => 'MTK',
        ]);

        $response->assertRedirect(route('admin.subjects.index'))
            ->assertSessionHas('success', 'Mapel berhasil diperbarui');

        $this->assertDatabaseHas('subjects', ['id' => $subject->id, 'name' => 'Matematika Wajib']);
    }

    public function test_destroy_subject_with_teacher_fails(): void
    {
        $subject = Subject::whereHas('teacherSubjects')->first();

        if ($subject === null) {
            $this->markTestSkipped('Tidak ada mapel dengan pengajar untuk diuji.');
        }

        $response = $this->actingAs($this->admin)->delete(route('admin.subjects.destroy', $subject));

        $response->assertSessionHasErrors();
        $this->assertSame('Mapel masih memiliki pengajar', session('errors')->first());
        $this->assertDatabaseHas('subjects', ['id' => $subject->id]);
    }

    public function test_destroy_subject_without_teacher_succeeds(): void
    {
        $subject = Subject::create([
            'name' => 'Biologi',
            'code' => 'BIO',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.subjects.destroy', $subject));

        $response->assertRedirect(route('admin.subjects.index'))
            ->assertSessionHas('success', 'Mapel berhasil dihapus');

        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }
}
