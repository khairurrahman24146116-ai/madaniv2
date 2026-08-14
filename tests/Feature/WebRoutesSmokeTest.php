<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\ScoreComponent;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebRoutesSmokeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $guru;

    private User $wali;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('email', 'admin@madani.id')->firstOrFail();
        $this->guru = User::where('email', 'ahmad@madani.id')->firstOrFail();
        $this->wali = User::where('role', 'wali_murid')->firstOrFail();
        $this->wali->update(['must_change_password' => false]);
    }

    public function test_home_and_login_pages(): void
    {
        $this->get('/')->assertOk();
        $this->get(route('login'))->assertOk();

        $this->actingAs($this->admin)->get('/')->assertRedirect(route('admin.dashboard'));
        $this->actingAs($this->admin)->get(route('login'))->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_view_all_refactored_admin_pages(): void
    {
        $classroom = Classroom::firstOrFail();
        $subject = Subject::firstOrFail();
        $student = Student::firstOrFail();
        $teacherSubject = TeacherSubject::firstOrFail();
        $schedule = Schedule::first();
        $scoreComponent = ScoreComponent::first();

        $routes = [
            route('profile.edit'),
            route('admin.dashboard'),
            route('admin.classrooms.index'),
            route('admin.classrooms.create'),
            route('admin.classrooms.edit', $classroom),
            route('admin.subjects.index'),
            route('admin.subjects.create'),
            route('admin.subjects.edit', $subject),
            route('admin.students.index'),
            route('admin.students.create'),
            route('admin.students.edit', $student),
            route('admin.students.move-form', $student),
            route('admin.teacher-subjects.index'),
            route('admin.teacher-subjects.create'),
            route('admin.teacher-subjects.edit', $teacherSubject),
            route('admin.schedules.index'),
            route('admin.schedules.create'),
            route('admin.score-components.index'),
            route('admin.score-components.create'),
            route('admin.teacher-attendances.index'),
            route('admin.users.index'),
            route('admin.users.create'),
            route('admin.users.edit', $this->admin),
            route('admin.activity-logs.index'),
        ];

        if ($schedule) {
            $routes[] = route('admin.schedules.edit', $schedule);
        }

        if ($scoreComponent) {
            $routes[] = route('admin.score-components.edit', $scoreComponent);
        }

        foreach ($routes as $uri) {
            $this->actingAs($this->admin)->get($uri)->assertOk();
        }
    }

    public function test_guru_can_view_all_refactored_guru_pages(): void
    {
        $classroomIds = $this->guru->teacherSubjects()->pluck('classroom_id');
        $student = Student::whereIn('classroom_id', $classroomIds)->firstOrFail();

        $routes = [
            route('profile.edit'),
            route('dashboard'),
            route('attendances.index'),
            route('attendances.form'),
            route('attendances.realtime'),
            route('schedules.index'),
            route('schedules.mobile'),
            route('scores.create'),
            route('scores.rapor-preview', ['student_id' => $student->id]),
            route('teacher.attendances.form'),
            route('teacher.attendances.index'),
        ];

        foreach ($routes as $uri) {
            $this->actingAs($this->guru)->get($uri)->assertOk();
        }
    }

    public function test_wali_murid_can_view_dashboard_and_rapor(): void
    {
        $student = Student::where('user_id', $this->wali->id)->firstOrFail();

        $this->actingAs($this->wali)->get(route('profile.edit'))->assertOk();
        $this->actingAs($this->wali)->get(route('wali-murid.dashboard'))->assertOk();
        $this->actingAs($this->wali)->get(route('wali-murid.rapor', $student))->assertOk();
    }
}
