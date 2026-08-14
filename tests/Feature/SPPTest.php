<?php

namespace Tests\Feature;

use App\Models\PaymentReceipt;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SPPTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $bendahara;

    private User $guru;

    private User $wali;

    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        Storage::fake('public');

        $this->admin = User::where('email', 'admin@madani.id')->firstOrFail();
        $this->bendahara = User::where('email', 'bendahara@madani.id')->firstOrFail();
        $this->guru = User::where('email', 'ahmad@madani.id')->firstOrFail();
        $this->student = Student::firstOrFail();
        $this->wali = User::factory()->waliMurid()->create(['must_change_password' => false]);
    }

    private function paymentPayload(array $overrides = []): array
    {
        return array_merge([
            'student_id' => $this->student->id,
            'month' => 8,
            'year' => 2026,
            'amount' => 150000,
            'method' => 'cash',
        ], $overrides);
    }

    private function postPaymentAs(User $user, array $payload): TestResponse
    {
        Sanctum::actingAs($user);

        return $this->post('/app/spp/bayar', array_merge($payload, [
            'proof' => UploadedFile::fake()->image('bukti.jpg'),
        ]));
    }

    public function test_bendahara_can_mark_fee_as_paid(): void
    {
        $response = $this->postPaymentAs($this->bendahara, $this->paymentPayload());

        $response->assertRedirect(route('spp.index', ['month' => 8, 'year' => 2026]));

        $this->assertDatabaseHas('student_fees', [
            'student_id' => $this->student->id,
            'month' => 8,
            'year' => 2026,
            'is_paid' => true,
        ]);

        $receipt = PaymentReceipt::where('student_id', $this->student->id)
            ->where('month', 8)
            ->where('year', 2026)
            ->whereNull('reversal_of')
            ->firstOrFail();

        $this->assertEquals(150000, (float) $receipt->amount);
        $this->assertEquals('cash', $receipt->method);
        $this->assertNotNull($receipt->receipt_number);
        $this->assertNotNull($receipt->proof_path);
        $this->assertEquals($this->bendahara->id, $receipt->recorded_by);
    }

    public function test_admin_cannot_mark_fee_as_paid(): void
    {
        $response = $this->postPaymentAs($this->admin, $this->paymentPayload());

        $response->assertStatus(403);

        $this->assertDatabaseMissing('payment_receipts', [
            'student_id' => $this->student->id,
        ]);
    }

    public function test_guru_cannot_mark_fee_as_paid(): void
    {
        $response = $this->postPaymentAs($this->guru, $this->paymentPayload());

        $response->assertStatus(403);

        $this->assertDatabaseMissing('payment_receipts', [
            'student_id' => $this->student->id,
        ]);
    }

    public function test_wali_murid_cannot_mark_fee_as_paid(): void
    {
        $response = $this->postPaymentAs($this->wali, $this->paymentPayload());

        $response->assertStatus(403);

        $this->assertDatabaseMissing('payment_receipts', [
            'student_id' => $this->student->id,
        ]);
    }

    public function test_payment_requires_proof(): void
    {
        Sanctum::actingAs($this->bendahara);

        $response = $this->post('/app/spp/bayar', $this->paymentPayload());

        $response->assertSessionHasErrors('proof');

        $this->assertDatabaseMissing('payment_receipts', ['student_id' => $this->student->id]);
    }

    public function test_receipt_number_is_sequential(): void
    {
        $this->postPaymentAs($this->bendahara, $this->paymentPayload(['month' => 1]));
        $this->postPaymentAs($this->bendahara, $this->paymentPayload(['month' => 2]));

        $receipts = PaymentReceipt::orderBy('receipt_number')->get();

        $this->assertCount(2, $receipts);
        $this->assertEquals('INV/2026/000001', $receipts[0]->receipt_number);
        $this->assertEquals('INV/2026/000002', $receipts[1]->receipt_number);
    }

    public function test_bendahara_can_cancel_fee_via_reversal(): void
    {
        $this->postPaymentAs($this->bendahara, $this->paymentPayload());

        $fee = StudentFee::where('student_id', $this->student->id)
            ->where('month', 8)
            ->where('year', 2026)
            ->firstOrFail();

        Sanctum::actingAs($this->bendahara);
        $this->post("/app/spp/{$fee->id}/batal", [
            'reason' => 'Pembayaran ganda',
        ])->assertRedirect(route('spp.index', ['month' => 8, 'year' => 2026]));

        $this->assertDatabaseHas('student_fees', [
            'id' => $fee->id,
            'is_paid' => false,
        ]);

        $original = PaymentReceipt::where('student_id', $this->student->id)
            ->where('month', 8)
            ->where('year', 2026)
            ->whereNull('reversal_of')
            ->firstOrFail();

        $reversal = PaymentReceipt::where('student_id', $this->student->id)
            ->where('month', 8)
            ->where('year', 2026)
            ->where('reversal_of', $original->id)
            ->firstOrFail();

        $this->assertEquals('INV/2026/000002', $reversal->receipt_number);
        $this->assertStringContainsString('Pembayaran ganda', (string) $reversal->note);

        // Kwitansi asli tetap tersimpan (append-only).
        $this->assertNotNull($original->id);
    }

    public function test_admin_cannot_cancel_fee(): void
    {
        $this->postPaymentAs($this->bendahara, $this->paymentPayload());

        $fee = StudentFee::where('student_id', $this->student->id)
            ->where('month', 8)
            ->where('year', 2026)
            ->firstOrFail();

        Sanctum::actingAs($this->admin);
        $this->post("/app/spp/{$fee->id}/batal", ['reason' => 'x'])
            ->assertStatus(403);

        $this->assertDatabaseHas('student_fees', ['id' => $fee->id, 'is_paid' => true]);
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

        Sanctum::actingAs($this->wali);
        $this->post("/app/spp/{$fee->id}/batal", ['reason' => 'x'])
            ->assertStatus(403);
    }

    public function test_receipts_have_no_update_or_delete_routes(): void
    {
        $this->postPaymentAs($this->bendahara, $this->paymentPayload());

        Sanctum::actingAs($this->bendahara);
        $this->put('/app/spp/bayar', $this->paymentPayload())
            ->assertStatus(405);

        Sanctum::actingAs($this->bendahara);
        $this->delete('/app/spp/bayar')
            ->assertStatus(405);
    }

    public function test_wali_murid_can_view_payer_page_with_payment_accounts(): void
    {
        $student = Student::factory()->create(['user_id' => $this->wali->id]);
        StudentFee::create([
            'student_id' => $student->id,
            'month' => now()->month,
            'year' => now()->year,
            'amount' => 150000,
            'is_paid' => false,
        ]);

        Sanctum::actingAs($this->wali);
        $response = $this->get(route('spp.payer'));

        $response->assertOk();
        $response->assertSee('Cara Membayar SPP');
        $response->assertSee(config('school.payment_instructions'));
        $response->assertSee(config('school.payment_accounts')[0]['bank']);
        $response->assertSee(config('school.payment_accounts')[0]['account_number']);
        $response->assertDontSee('Menunggu konfirmasi pembayaran');
    }

    public function test_wali_murid_cannot_mark_paid_via_payer_page_form(): void
    {
        $student = Student::factory()->create(['user_id' => $this->wali->id]);
        StudentFee::create([
            'student_id' => $student->id,
            'month' => now()->month,
            'year' => now()->year,
            'amount' => 150000,
            'is_paid' => false,
        ]);

        Sanctum::actingAs($this->wali);
        $this->get(route('spp.payer'))
            ->assertOk()
            ->assertDontSee('Catat & Terbitkan Kwitansi');
    }

    public function test_wali_dashboard_bayar_button_links_to_payer_page(): void
    {
        $student = Student::factory()->create(['user_id' => $this->wali->id]);
        StudentFee::create([
            'student_id' => $student->id,
            'month' => now()->month,
            'year' => now()->year,
            'amount' => 150000,
            'is_paid' => false,
        ]);

        Sanctum::actingAs($this->wali);
        $this->get(route('wali-murid.dashboard'))
            ->assertOk()
            ->assertSee(route('spp.payer'));
    }
}
