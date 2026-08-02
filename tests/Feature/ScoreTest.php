<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ScoreTest extends TestCase
{
    private string $guruToken;

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $guru = User::where('email', 'ahmad@madani.id')->first();
        $this->guruToken = $guru->createToken('test')->plainTextToken;
    }

    public function test_store(): void
    {
        $response = $this->withToken($this->guruToken)->postJson('/scores', [
            'student_id' => 1,
            'subject_id' => 1,
            'component_code' => 'tugas',
            'value' => 85,
            'description' => 'Tugas 2',
            'semester' => 'ganjil',
            'academic_year' => '2025/2026',
        ]);
        $response->assertStatus(201)->assertJsonPath('success', true);
    }

    public function test_batch_store(): void
    {
        $response = $this->withToken($this->guruToken)->postJson('/scores/batch', [
            'subject_id' => 1,
            'component_code' => 'ph',
            'semester' => 'ganjil',
            'academic_year' => '2025/2026',
            'scores' => [
                ['student_id' => 1, 'value' => 80],
                ['student_id' => 2, 'value' => 90],
            ],
        ]);
        $response->assertStatus(201)->assertJsonPath('success', true);
    }

    public function test_guru_cannot_input_scores_for_same_subject_in_another_teachers_class(): void
    {
        $guruLain = User::where('email', 'fatimah@madani.id')->firstOrFail();
        $matematika = Subject::where('code', 'MTK')->firstOrFail();
        $kelasXii = Classroom::where('name', 'XII IPA 1')->firstOrFail();
        $siswaXii = Student::where('classroom_id', $kelasXii->id)->firstOrFail();

        TeacherSubject::create([
            'user_id' => $guruLain->id,
            'subject_id' => $matematika->id,
            'classroom_id' => $kelasXii->id,
        ]);

        $response = $this->withToken($this->guruToken)->postJson('/scores', [
            'student_id' => $siswaXii->id,
            'subject_id' => $matematika->id,
            'component_code' => 'tugas',
            'value' => 85,
            'semester' => 'ganjil',
            'academic_year' => '2025/2026',
        ]);

        $response->assertStatus(403);
    }

    public function test_show(): void
    {
        $score = Score::first();

        $response = $this->withToken($this->guruToken)->getJson("/scores/{$score->id}");
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_update(): void
    {
        $score = Score::first();

        $response = $this->withToken($this->guruToken)->putJson("/scores/{$score->id}", [
            'value' => 95,
        ]);
        $response->assertStatus(200)->assertJsonPath('success', true);
        $this->assertEquals(95, $score->fresh()->value);
    }

    public function test_destroy(): void
    {
        $score = Score::first();

        $response = $this->withToken($this->guruToken)->deleteJson("/scores/{$score->id}");
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_final_grade(): void
    {
        $response = $this->withToken($this->guruToken)
            ->getJson('/scores/final-grade?student_id=1&subject_id=1&semester=ganjil&academic_year=2025/2026');
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_batch_final_grade(): void
    {
        $subject = Subject::first();
        $classroom = Classroom::first();

        $response = $this->withToken($this->guruToken)
            ->getJson('/scores/batch-final-grade?classroom_id='.$classroom->id.'&subject_id='.$subject->id.'&semester=ganjil&academic_year=2025/2026');
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_rapor(): void
    {
        $response = $this->withToken($this->guruToken)
            ->getJson('/scores/rapor?student_id=1&semester=ganjil&academic_year=2025/2026');
        $response->assertStatus(200)->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'student' => ['id', 'name', 'nis'],
                'classroom' => ['id', 'name', 'grade'],
                'semester',
                'academic_year',
                'subjects' => [
                    '*' => ['subject_id', 'subject_name', 'subject_code', 'components', 'total_weight', 'final_grade', 'passed'],
                ],
                'overall_average',
                'passed_all',
                'attendance' => ['total', 'H', 'S', 'I', 'A'],
                'generated_at',
            ],
        ]);
        $this->assertNotNull($response->json('data.overall_average'));
        $this->assertIsArray($response->json('data.subjects'));
        $this->assertGreaterThan(0, count($response->json('data.subjects')));
    }

    public function test_rapor_wali_murid_can_only_see_own_child(): void
    {
        $student = Student::find(1);
        $waliToken = $student->user->createToken('test')->plainTextToken;

        $response = $this->withToken($waliToken)
            ->getJson('/scores/rapor?student_id=1&semester=ganjil&academic_year=2025/2026');
        $response->assertStatus(200)->assertJsonPath('success', true);

        $response = $this->withToken($waliToken)
            ->getJson('/scores/rapor?student_id=2&semester=ganjil&academic_year=2025/2026');
        $response->assertStatus(403);
    }

    public function test_guru_cannot_view_rapor_of_student_outside_taught_class(): void
    {
        $guru = User::where('email', 'ahmad@madani.id')->firstOrFail();
        $taughtClassroomIds = $guru->teacherSubjects()->pluck('classroom_id')->unique();

        $awayStudent = Student::whereNotIn('classroom_id', $taughtClassroomIds)->first();

        $this->assertNotNull($awayStudent, 'Butuh siswa di luar kelas yang diajar guru');

        $response = $this->withToken($this->guruToken)
            ->getJson('/scores/rapor?student_id='.$awayStudent->id.'&semester=ganjil&academic_year=2025/2026');
        $response->assertStatus(403);
    }

    public function test_guru_cannot_filter_index_by_student_outside_taught_class(): void
    {
        $guru = User::where('email', 'ahmad@madani.id')->firstOrFail();
        $taughtClassroomIds = $guru->teacherSubjects()->pluck('classroom_id')->unique();
        $awayStudent = Student::whereNotIn('classroom_id', $taughtClassroomIds)->firstOrFail();

        $this->withToken($this->guruToken)
            ->getJson('/scores?student_id='.$awayStudent->id)
            ->assertForbidden();
    }

    public function test_admin_can_access_any_students_rapor(): void
    {
        $admin = User::where('email', 'admin@madani.id')->firstOrFail();
        $adminToken = $admin->createToken('test')->plainTextToken;

        $this->withToken($adminToken)
            ->getJson('/scores/rapor?student_id=1&semester=ganjil&academic_year=2025/2026')
            ->assertOk();
    }

    public function test_rapor_pdf(): void
    {
        $response = $this->withToken($this->guruToken)
            ->get('/scores/rapor-pdf?student_id=1&semester=ganjil&academic_year=2025/2026');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('rapor_1001_ganjil_2025-2026.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_guru_cannot_preview_rapor_of_student_outside_taught_class(): void
    {
        $guru = User::where('email', 'ahmad@madani.id')->firstOrFail();
        $taughtClassroomIds = $guru->teacherSubjects()->pluck('classroom_id')->unique();
        $awayStudent = Student::whereNotIn('classroom_id', $taughtClassroomIds)->firstOrFail();

        Sanctum::actingAs($guru);

        $this->get('/app/scores/rapor-preview?student_id='.$awayStudent->id)
            ->assertForbidden();
    }

    public function test_export_csv(): void
    {
        $response = $this->withToken($this->guruToken)->getJson('/scores/export-csv');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }
}
