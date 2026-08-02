<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SPPTest extends TestCase
{
    use RefreshDatabase;

    private string $adminToken;

    private string $guruToken;

    private string $waliToken;

    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@madani.id')->first();
        $this->adminToken = $admin->createToken('test')->plainTextToken;

        $guru = User::where('email', 'ahmad@madani.id')->first();
        $this->guruToken = $guru->createToken('test')->plainTextToken;

        $this->student = Student::first();
        $wali = User::factory()->waliMurid()->create(['must_change_password' => false]);
        $this->waliToken = $wali->createToken('test')->plainTextToken;
    }

    public function test_admin_can_mark_fee_as_paid(): void
    {
        $response = $this->withToken($this->adminToken)->post('/app/spp/bayar', [
            'student_id' => $this->student->id,
            'month' => 8,
            'year' => 2026,
            'amount' => 150000,
        ]);

        $response->assertRedirect(route('spp.index', ['month' => 8, 'year' => 2026]));

        $this->assertDatabaseHas('student_fees', [
            'student_id' => $this->student->id,
            'month' => 8,
            'year' => 2026,
            'is_paid' => true,
        ]);
    }

    public function test_guru_cannot_mark_fee_as_paid(): void
    {
        $response = $this->withToken($this->guruToken)->post('/app/spp/bayar', [
            'student_id' => $this->student->id,
            'month' => 8,
            'year' => 2026,
            'amount' => 150000,
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('student_fees', [
            'student_id' => $this->student->id,
            'month' => 8,
            'year' => 2026,
        ]);
    }

    public function test_wali_murid_cannot_mark_fee_as_paid(): void
    {
        $response = $this->withToken($this->waliToken)->post('/app/spp/bayar', [
            'student_id' => $this->student->id,
            'month' => 8,
            'year' => 2026,
            'amount' => 150000,
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('student_fees', [
            'student_id' => $this->student->id,
            'month' => 8,
            'year' => 2026,
        ]);
    }

    public function test_admin_can_cancel_fee(): void
    {
        $fee = StudentFee::create([
            'student_id' => $this->student->id,
            'month' => 8,
            'year' => 2026,
            'amount' => 150000,
            'is_paid' => true,
            'paid_at' => now(),
        ]);

        $response = $this->withToken($this->adminToken)->post("/app/spp/{$fee->id}/batal");

        $response->assertRedirect(route('spp.index', ['month' => 8, 'year' => 2026]));

        $this->assertDatabaseHas('student_fees', [
            'id' => $fee->id,
            'is_paid' => false,
        ]);
    }

    public function test_wali_murid_cannot_cancel_fee(): void
    {
        $fee = StudentFee::create([
            'student_id' => $this->student->id,
            'month' => 8,
            'year' => 2026,
            'amount' => 150000,
            'is_paid' => true,
            'paid_at' => now(),
        ]);

        $response = $this->withToken($this->waliToken)->post("/app/spp/{$fee->id}/batal");

        $response->assertStatus(403);
    }
}
