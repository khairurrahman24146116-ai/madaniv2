<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserToggleActiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_deactivate_guru(): void
    {
        $admin = User::where('email', 'admin@madani.id')->firstOrFail();
        $guru = User::where('email', 'ahmad@madani.id')->firstOrFail();

        Sanctum::actingAs($admin);

        $this->post(route('admin.users.toggle-active', $guru))
            ->assertRedirect();

        $this->assertFalse($guru->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_self(): void
    {
        $admin = User::where('email', 'admin@madani.id')->firstOrFail();

        Sanctum::actingAs($admin);

        $this->post(route('admin.users.toggle-active', $admin))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_last_active_admin(): void
    {
        $admin = User::where('email', 'admin@madani.id')->firstOrFail();
        $otherAdmin = User::factory()->admin()->create(['is_active' => true]);

        Sanctum::actingAs($admin);

        $this->post(route('admin.users.toggle-active', $otherAdmin))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertTrue($otherAdmin->fresh()->is_active);
    }

    public function test_deactivated_user_cannot_login(): void
    {
        $admin = User::where('email', 'admin@madani.id')->firstOrFail();
        $guru = User::where('email', 'ahmad@madani.id')->firstOrFail();

        Sanctum::actingAs($admin);

        $this->post(route('admin.users.toggle-active', $guru));

        $this->postJson('/auth/login', [
            'email' => $guru->email,
            'password' => 'guru123',
        ])->assertStatus(403);
    }
}
