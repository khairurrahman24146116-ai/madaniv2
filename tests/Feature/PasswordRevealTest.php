<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordRevealTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('email', 'admin@madani.id')->firstOrFail();
    }

    public function test_admin_reset_redirects_to_reveal_page_with_password_and_forces_change(): void
    {
        $target = User::where('email', 'ahmad@madani.id')->firstOrFail();

        $response = $this->actingAs($this->admin)->post(route('admin.users.reset-password', $target), [
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $response->assertRedirect(route('admin.users.password-reveal', $target));

        $reveal = $this->actingAs($this->admin)->get(route('admin.users.password-reveal', $target));
        $reveal->assertOk()->assertSee('rahasia123');

        $target->refresh();
        $this->assertTrue($target->must_change_password);
    }

    public function test_reveal_page_shows_password_only_once(): void
    {
        $target = User::where('email', 'ahmad@madani.id')->firstOrFail();

        $this->actingAs($this->admin)->post(route('admin.users.reset-password', $target), [
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $first = $this->actingAs($this->admin)->get(route('admin.users.password-reveal', $target));
        $first->assertOk()->assertSee('rahasia123');

        $second = $this->actingAs($this->admin)->get(route('admin.users.password-reveal', $target));
        $second->assertRedirect(route('admin.users.index'));
    }

    public function test_reveal_page_redirects_when_session_missing(): void
    {
        $target = User::where('email', 'ahmad@madani.id')->firstOrFail();

        $this->actingAs($this->admin)->get(route('admin.users.password-reveal', $target))
            ->assertRedirect(route('admin.users.index'));
    }

    public function test_reveal_page_redirects_when_session_belongs_to_another_user(): void
    {
        $target = User::where('email', 'ahmad@madani.id')->firstOrFail();
        $other = User::where('email', 'fatimah@madani.id')->firstOrFail();

        $this->actingAs($this->admin)->post(route('admin.users.reset-password', $target), [
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $this->actingAs($this->admin)->get(route('admin.users.password-reveal', $other))
            ->assertRedirect(route('admin.users.index'));
    }

    public function test_guru_cannot_reset_password_or_access_reveal_page(): void
    {
        $guru = User::where('email', 'ahmad@madani.id')->firstOrFail();
        $target = User::where('email', 'fatimah@madani.id')->firstOrFail();

        $this->actingAs($guru)
            ->post(route('admin.users.reset-password', $target), [
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])
            ->assertForbidden();

        $this->actingAs($guru)
            ->get(route('admin.users.password-reveal', $target))
            ->assertForbidden();
    }
}
