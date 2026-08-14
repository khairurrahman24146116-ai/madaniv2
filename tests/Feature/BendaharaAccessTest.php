<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BendaharaAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_bendahara_redirected_to_finance_dashboard_after_web_login(): void
    {
        $this->post('/auth/login/web', [
            'email' => 'bendahara@madani.id',
            'password' => 'bendahara123',
        ])->assertRedirect(route('bendahara.dashboard'));
    }

    public function test_bendahara_is_recognized_by_model(): void
    {
        $bendahara = User::where('email', 'bendahara@madani.id')->firstOrFail();

        $this->assertTrue($bendahara->isBendahara());
        $this->assertFalse($bendahara->isAdmin());
    }

    public function test_bendahara_can_open_finance_dashboard(): void
    {
        $bendahara = User::where('email', 'bendahara@madani.id')->firstOrFail();
        Sanctum::actingAs($bendahara);

        $this->get(route('bendahara.dashboard'))->assertOk();
        $this->get(route('bendahara.rekap'))->assertOk();
    }

    public function test_admin_cannot_open_finance_dashboard(): void
    {
        $admin = User::where('email', 'admin@madani.id')->firstOrFail();
        Sanctum::actingAs($admin);

        $this->get(route('bendahara.dashboard'))->assertStatus(403);
        $this->get(route('bendahara.rekap'))->assertStatus(403);
    }

    public function test_bendahara_cannot_open_admin_dashboard(): void
    {
        $bendahara = User::where('email', 'bendahara@madani.id')->firstOrFail();
        Sanctum::actingAs($bendahara);

        $this->get(route('admin.dashboard'))->assertStatus(403);
    }

    public function test_guru_cannot_open_finance_dashboard(): void
    {
        $guru = User::where('email', 'ahmad@madani.id')->firstOrFail();
        Sanctum::actingAs($guru);

        $this->get(route('bendahara.dashboard'))->assertStatus(403);
    }

    public function test_wali_murid_cannot_open_finance_dashboard(): void
    {
        $wali = User::factory()->waliMurid()->create();
        Sanctum::actingAs($wali);

        $this->get(route('bendahara.dashboard'))->assertStatus(403);
    }
}
