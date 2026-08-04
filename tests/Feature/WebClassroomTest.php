<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebClassroomTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('email', 'admin@madani.id')->first();
    }

    public function test_admin_can_view_classroom_pages(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.classrooms.index'))
            ->assertOk();

        $this->actingAs($this->admin)
            ->get(route('admin.classrooms.create'))
            ->assertOk();

        $this->actingAs($this->admin)
            ->get(route('admin.classrooms.edit', Classroom::first()))
            ->assertOk();
    }

    public function test_guru_cannot_access_admin_classroom_pages(): void
    {
        $guru = User::where('email', 'ahmad@madani.id')->first();

        $this->actingAs($guru)
            ->get(route('admin.classrooms.index'))
            ->assertForbidden();
    }

    public function test_store_creates_classroom_and_redirects(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.classrooms.store'), [
            'name' => 'X IPA 2',
            'grade' => 'X',
            'academic_year' => '2025/2026',
        ]);

        $response->assertRedirect(route('admin.classrooms.index'))
            ->assertSessionHas('success', 'Kelas berhasil ditambahkan');

        $this->assertDatabaseHas('classrooms', ['name' => 'X IPA 2', 'grade' => 'X']);
    }

    public function test_update_classroom_and_redirects(): void
    {
        $classroom = Classroom::first();

        $response = $this->actingAs($this->admin)->put(route('admin.classrooms.update', $classroom), [
            'name' => 'X IPA 1 Revisi',
            'grade' => 'X',
            'academic_year' => '2025/2026',
        ]);

        $response->assertRedirect(route('admin.classrooms.index'))
            ->assertSessionHas('success', 'Kelas berhasil diperbarui');

        $this->assertDatabaseHas('classrooms', ['id' => $classroom->id, 'name' => 'X IPA 1 Revisi']);
    }

    public function test_destroy_classroom_with_students_fails(): void
    {
        $classroom = Classroom::withCount('students')->first();

        if ($classroom->students_count === 0) {
            $this->markTestSkipped('Tidak ada kelas dengan siswa untuk diuji.');
        }

        $response = $this->actingAs($this->admin)->delete(route('admin.classrooms.destroy', $classroom));

        $response->assertSessionHasErrors();
        $this->assertSame('Kelas masih memiliki siswa', session('errors')->first());
        $this->assertDatabaseHas('classrooms', ['id' => $classroom->id]);
    }

    public function test_destroy_classroom_without_students_succeeds(): void
    {
        $classroom = Classroom::create([
            'name' => 'XII IPA 9',
            'grade' => 'XII',
            'academic_year' => '2025/2026',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.classrooms.destroy', $classroom));

        $response->assertRedirect(route('admin.classrooms.index'))
            ->assertSessionHas('success', 'Kelas berhasil dihapus');

        $this->assertDatabaseMissing('classrooms', ['id' => $classroom->id]);
    }
}
