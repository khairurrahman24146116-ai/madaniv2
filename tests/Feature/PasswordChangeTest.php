<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_user_with_flag_is_redirected_from_protected_pages(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('rahasia123'),
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($user)->get('/app/dashboard');

        $response->assertRedirect(route('password.change'));
    }

    public function test_change_password_page_is_accessible(): void
    {
        $user = User::factory()->create(['must_change_password' => true]);

        $response = $this->actingAs($user)->get(route('password.change'));

        $response->assertOk();
    }

    public function test_user_without_flag_is_not_redirected(): void
    {
        $user = User::factory()->create(['must_change_password' => false]);

        $response = $this->actingAs($user)->get('/app/dashboard');

        $response->assertOk();
    }

    public function test_change_password_success_updates_flag(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('rahasia123'),
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($user)->post(route('password.change.update'), [
            'current_password' => 'rahasia123',
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'must_change_password' => false,
        ]);

        $this->assertTrue(Hash::check('passwordbaru', $user->fresh()->password));
    }

    public function test_change_password_with_wrong_current_fails(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('rahasia123'),
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($user)->post(route('password.change.update'), [
            'current_password' => 'salah',
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
        ]);

        $response->assertSessionHasErrors('current_password');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'must_change_password' => true,
        ]);
    }

    public function test_after_change_user_can_access_protected_pages(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('rahasia123'),
            'must_change_password' => true,
        ]);

        $this->actingAs($user)->post(route('password.change.update'), [
            'current_password' => 'rahasia123',
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
        ]);

        $response = $this->actingAs($user->fresh())->get('/app/dashboard');

        $response->assertOk();
    }

    public function test_api_login_includes_must_change_password_flag(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('rahasia123'),
            'must_change_password' => true,
        ]);

        $response = $this->postJson('/auth/login', [
            'email' => $user->email,
            'password' => 'rahasia123',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.user.must_change_password', true);
    }
}
