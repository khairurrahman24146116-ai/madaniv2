<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserManageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::where('email', 'admin@madani.id')->firstOrFail();

        Sanctum::actingAs($admin);

        $this->post(route('admin.users.store'), [
            'name' => 'Ustaz Baru',
            'email' => 'baru@madani.id',
            'role' => 'guru',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 1',
        ])->assertRedirect(route('admin.users.password-reveal', User::where('email', 'baru@madani.id')->firstOrFail()));

        $user = User::where('email', 'baru@madani.id')->firstOrFail();

        $this->assertSame('Ustaz Baru', $user->name);
        $this->assertSame('guru', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->must_change_password);
        $this->assertSame('081234567890', $user->phone);
    }

    public function test_create_user_requires_confirmed_password(): void
    {
        $admin = User::where('email', 'admin@madani.id')->firstOrFail();

        Sanctum::actingAs($admin);

        $this->post(route('admin.users.store'), [
            'name' => 'Ustaz Baru',
            'email' => 'baru@madani.id',
            'role' => 'guru',
            'password' => 'rahasia123',
            'password_confirmation' => 'beda-password',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'baru@madani.id']);
    }

    public function test_admin_can_edit_user(): void
    {
        $admin = User::where('email', 'admin@madani.id')->firstOrFail();
        $guru = User::where('email', 'ahmad@madani.id')->firstOrFail();

        Sanctum::actingAs($admin);

        $this->put(route('admin.users.update', $guru), [
            'name' => 'Ustaz Ahmad Updated',
            'email' => 'ahmad@madani.id',
            'role' => 'guru',
            'phone' => '081212345678',
            'address' => 'Jl. Baru',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame('Ustaz Ahmad Updated', $guru->fresh()->name);
        $this->assertSame('081212345678', $guru->fresh()->phone);
    }

    public function test_admin_cannot_change_role_of_guru_with_subject_mapping(): void
    {
        $admin = User::where('email', 'admin@madani.id')->firstOrFail();
        $guru = User::where('email', 'ahmad@madani.id')->firstOrFail();

        $this->assertTrue($guru->teacherSubjects()->exists());

        Sanctum::actingAs($admin);

        $this->put(route('admin.users.update', $guru), [
            'name' => $guru->name,
            'email' => $guru->email,
            'role' => 'bendahara',
        ])->assertRedirect()->assertSessionHas('error');

        $this->assertSame('guru', $guru->fresh()->role);
    }

    public function test_admin_cannot_change_role_of_wali_with_students(): void
    {
        $admin = User::where('email', 'admin@madani.id')->firstOrFail();
        $wali = User::where('role', 'wali_murid')->whereHas('students')->firstOrFail();

        Sanctum::actingAs($admin);

        $this->put(route('admin.users.update', $wali), [
            'name' => $wali->name,
            'email' => $wali->email,
            'role' => 'guru',
        ])->assertRedirect()->assertSessionHas('error');

        $this->assertSame('wali_murid', $wali->fresh()->role);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::where('email', 'admin@madani.id')->firstOrFail();

        Sanctum::actingAs($admin);

        $this->delete(route('admin.users.destroy', $admin))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['email' => 'admin@madani.id']);
    }

    public function test_admin_cannot_delete_last_active_admin(): void
    {
        $admin = User::where('email', 'admin@madani.id')->firstOrFail();
        $otherAdmin = User::factory()->admin()->create(['is_active' => true]);

        Sanctum::actingAs($admin);

        $this->delete(route('admin.users.destroy', $otherAdmin))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['email' => $otherAdmin->email]);
    }

    public function test_admin_cannot_delete_user_with_related_data(): void
    {
        $admin = User::where('email', 'admin@madani.id')->firstOrFail();
        $guru = User::where('email', 'ahmad@madani.id')->firstOrFail();

        Sanctum::actingAs($admin);

        $this->delete(route('admin.users.destroy', $guru))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['email' => $guru->email]);
    }

    public function test_admin_can_delete_user_without_related_data(): void
    {
        $admin = User::where('email', 'admin@madani.id')->firstOrFail();
        $guru = User::factory()->guru()->create(['email' => 'baru@madani.id']);

        Sanctum::actingAs($admin);

        $this->delete(route('admin.users.destroy', $guru))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['email' => 'baru@madani.id']);
    }
}
