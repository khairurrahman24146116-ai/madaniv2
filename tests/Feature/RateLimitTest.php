<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_web_is_throttled_after_exceeding_limit(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/auth/login/web', [
                'email' => 'tidak-ada@madani.id',
                'password' => 'salah',
            ]);
        }

        $this->post('/auth/login/web', [
            'email' => 'tidak-ada@madani.id',
            'password' => 'salah',
        ])->assertStatus(429);
    }

    public function test_forgot_password_is_throttled_after_exceeding_limit(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->post('/forgot-password', ['email' => 'tidak-ada@madani.id']);
        }

        $this->post('/forgot-password', ['email' => 'tidak-ada@madani.id'])
            ->assertStatus(429);
    }

    public function test_reset_password_is_throttled_after_exceeding_limit(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/reset-password', [
                'token' => 'invalid',
                'email' => 'tidak-ada@madani.id',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ]);
        }

        $this->post('/reset-password', [
            'token' => 'invalid',
            'email' => 'tidak-ada@madani.id',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertStatus(429);
    }

    public function test_admin_reset_password_is_throttled_after_exceeding_limit(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['role' => 'guru']);

        for ($i = 0; $i < 10; $i++) {
            $this->actingAs($admin)->post('/app/admin/users/'.$target->id.'/reset-password', [
                'password' => 'rahasia123',
            ]);
        }

        $this->actingAs($admin)->post('/app/admin/users/'.$target->id.'/reset-password', [
            'password' => 'rahasia123',
        ])->assertStatus(429);
    }
}
