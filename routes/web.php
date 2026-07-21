<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Score;
use App\Models\ScoreComponent;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
});

// ===== Web Login =====
Route::post('/auth/login/web', [AuthController::class, 'loginWeb'])->name('auth.login.web');
Route::post('/auth/logout/web', [AuthController::class, 'logoutWeb'])->name('auth.logout.web');

Route::get('/login', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('login');

// ===== Password Reset =====
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [PasswordResetController::class, 'forgotPassword'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'updatePassword'])->name('password.update');
});

// ===== Halaman View (dengan middleware auth) =====
Route::middleware('auth:sanctum')->group(function () {

    // ===== Wali Murid: Dashboard & Rapor (tidak di-nesting di dalam admin,guru) =====
    Route::middleware('role:wali_murid')->group(function () {
        Route::get('/app/wali-murid', function () {
            $students = Student::with('classroom')->where('user_id', auth()->id())->get();

            return view('wali-murid.dashboard', compact('students'));
        })->name('wali-murid.dashboard');

        Route::get('/app/wali-murid/rapor/{student}', function (Student $student) {
            if ($student->user_id !== auth()->id()) {
                abort(403);
            }

            $subjects = Subject::with('scoreComponents')->get();
            $rapor = collect();

            foreach ($subjects as $subject) {
                $scores = Score::where('student_id', $student->id)
                    ->where('subject_id', $subject->id)
                    ->get();

                if ($scores->isEmpty()) {
                    continue;
                }

                $components = $subject->scoreComponents;
                $componentData = [];

                foreach ($components as $comp) {
                    $compScores = $scores->where('component_code', $comp->code);
                    if ($compScores->isEmpty()) {
                        continue;
                    }

                    $avgValue = $compScores->avg('value');
                    $weighted = ($avgValue * $comp->weight) / 100;

                    $componentData[] = [
                        'name' => $comp->name,
                        'value' => $avgValue,
                        'weight' => $comp->weight,
                        'weighted' => $weighted,
                    ];
                }

                if (empty($componentData)) {
                    continue;
                }

                $finalGrade = array_sum(array_column($componentData, 'weighted'));

                $rapor->push([
                    'subject' => $subject->name,
                    'final_grade' => $finalGrade,
                    'components' => $componentData,
                ]);
            }

            return view('wali-murid.rapor', compact('student', 'rapor'));
        })->name('wali-murid.rapor');
    });

    // ===== Guru & Admin: dashboard dan operasional mengajar =====
    Route::middleware('role:admin,guru')->group(function () {

        Route::get('/app/dashboard', function () {
            $schedules = Schedule::with('teacherSubject.subject', 'teacherSubject.classroom', 'teacherSubject.user')
                ->where('day', now()->locale('id')->isoFormat('dddd'))
                ->when(! auth()->user()->isAdmin(), fn ($query) => $query->whereHas('teacherSubject', fn ($q) => $q->where('user_id', auth()->id())))
                ->get()
                ->unique(fn ($schedule) => implode('|', [$schedule->day, $schedule->start_time, $schedule->teacher_subject_id]));

            $activeSchedule = $schedules->first();

            return view('dashboard', [
                'todaySessions' => $schedules->count(),
                'upcomingSchedules' => $schedules->take(3)->map(fn ($s) => [
                    'time' => $s->start_time.' - '.$s->end_time,
                    'subject' => $s->teacherSubject->subject->name ?? '',
                    'class' => $s->teacherSubject->classroom->name ?? '',
                    'type' => 'TEORI',
                ]),
                'activeClass' => $activeSchedule
                    ? ($activeSchedule->teacherSubject->subject->name ?? '').' - '.($activeSchedule->teacherSubject->classroom->name ?? '')
                    : null,
                'activeRoom' => $activeSchedule ? ($activeSchedule->teacherSubject->classroom->name ?? '') : null,
                'studentCount' => $activeSchedule?->teacherSubject->classroom->students()->count(),
                'pendingGrades' => null,
                'attendanceRate' => null,
            ]);
        })->name('dashboard');

        Route::get('/app/attendances', function () {
            $attendances = Attendance::with('student', 'schedule.teacherSubject.subject', 'schedule.teacherSubject.classroom')
                ->orderBy('date', 'desc')->paginate(20);

            return view('attendances.index', compact('attendances'));
        })->name('attendances.index');

        Route::get('/app/schedules', function () {
            $schedules = Schedule::with('teacherSubject.subject', 'teacherSubject.classroom', 'teacherSubject.user')->get();
            $scheduleGrid = $schedules->map(fn ($s) => [
                'day' => $s->day,
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'subject' => $s->teacherSubject->subject->name ?? '',
                'teacher' => $s->teacherSubject->user->name ?? '',
                'teacher_short' => substr($s->teacherSubject->user->name ?? '', 0, 10),
                'room' => 'Ruang '.($s->hour_order + 1),
            ]);

            return view('schedules.index', compact('scheduleGrid'));
        })->name('schedules.index');

        Route::get('/app/schedules/mobile', function () {
            $day = request('day', now()->locale('id')->isoFormat('dddd'));
            $currentDay = strtolower($day);
            $schedules = Schedule::with('teacherSubject.subject', 'teacherSubject.classroom', 'teacherSubject.user')
                ->where('day', $day)
                ->orWhereNull('day')
                ->get();

            return view('schedules.mobile', compact('schedules', 'currentDay'));
        })->name('schedules.mobile');

        Route::get('/app/scores/rapor-preview', function () {
            $studentId = request('student_id', Student::first()?->id);
            $student = Student::with('classroom')->find($studentId);
            $semester = request('semester', 'ganjil');
            $academicYear = request('academic_year', '2025/2026');

            $grades = collect();
            if ($student) {
                $subjects = Subject::with('scoreComponents')->get();
                foreach ($subjects as $subject) {
                    $scores = Score::where('student_id', $student->id)
                        ->where('subject_id', $subject->id)
                        ->where('semester', $semester)
                        ->where('academic_year', $academicYear)
                        ->get();

                    if ($scores->isEmpty()) {
                        continue;
                    }

                    $total = 0;
                    $totalWeight = 0;
                    foreach ($subject->scoreComponents as $comp) {
                        $compScores = $scores->where('component_code', $comp->code);
                        if ($compScores->isEmpty()) {
                            continue;
                        }
                        $avg = $compScores->avg('value');
                        $total += $avg * ($comp->weight / 100);
                        $totalWeight += $comp->weight;
                    }

                    if ($totalWeight > 0) {
                        $finalScore = round(($total / $totalWeight) * 100, 2);
                        $grades->push(['subject' => $subject->name, 'score' => $finalScore, 'kkm' => 70]);
                    }
                }

                $attendanceStats = [
                    'H' => Attendance::where('student_id', $student->id)->where('status', 'H')->count(),
                    'S' => Attendance::where('student_id', $student->id)->where('status', 'S')->count(),
                    'I' => Attendance::where('student_id', $student->id)->where('status', 'I')->count(),
                    'A' => Attendance::where('student_id', $student->id)->where('status', 'A')->count(),
                ];
            } else {
                $attendanceStats = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
            }

            $gpa = $grades->isNotEmpty() ? round($grades->avg('score') / 25, 2) : null;

            return view('scores.rapor-preview', compact('student', 'semester', 'academicYear', 'grades', 'attendanceStats', 'gpa'));
        })->name('scores.rapor-preview');

        Route::get('/app/attendances/form', function () {
            $schedules = Schedule::with('teacherSubject.subject', 'teacherSubject.classroom')
                ->when(! auth()->user()->isAdmin(), fn ($query) => $query->whereHas('teacherSubject', fn ($q) => $q->where('user_id', auth()->id())))
                ->get()
                ->unique(fn ($schedule) => implode('|', [$schedule->day, $schedule->start_time, $schedule->teacher_subject_id]));
            $scheduleId = request('schedule_id', $schedules->first()?->id);
            $date = request('date', now()->format('Y-m-d'));
            $schedule = $schedules->firstWhere('id', $scheduleId);
            $students = $schedule?->teacherSubject->classroom->students ?? collect();
            $canEdit = auth()->user()->isAdmin() || AttendanceController::isWithinSoreHours();

            return view('attendances.form', compact('schedules', 'schedule', 'students', 'date', 'canEdit'));
        })->name('attendances.form');

        Route::post('/app/attendances', [AttendanceController::class, 'store'])->name('attendances.store');

        Route::get('/app/attendances/realtime', function () {
            $schedules = Schedule::with('teacherSubject.subject', 'teacherSubject.classroom')
                ->when(! auth()->user()->isAdmin(), fn ($query) => $query->whereHas('teacherSubject', fn ($q) => $q->where('user_id', auth()->id())))
                ->get()
                ->unique(fn ($schedule) => implode('|', [$schedule->day, $schedule->start_time, $schedule->teacher_subject_id]));
            $scheduleId = request('schedule_id', $schedules->first()?->id);
            $date = request('date', now()->format('Y-m-d'));
            $schedule = $schedules->firstWhere('id', $scheduleId);
            $students = $schedule?->teacherSubject->classroom->students ?? collect();
            $canEdit = auth()->user()->isAdmin() || AttendanceController::isWithinSoreHours();

            return view('attendances.realtime', compact('schedules', 'schedule', 'students', 'date', 'canEdit'));
        })->name('attendances.realtime');

        Route::get('/app/scores/input', function () {
            $teacherSubjects = TeacherSubject::with(['subject', 'classroom'])
                ->when(! auth()->user()->isAdmin(), fn ($query) => $query->where('user_id', auth()->id()))
                ->orderBy('classroom_id')
                ->get();
            $selectedMapping = $teacherSubjects->firstWhere('id', request('teacher_subject_id')) ?? $teacherSubjects->first();
            $students = $selectedMapping
                ? Student::where('classroom_id', $selectedMapping->classroom_id)->where('is_active', true)->orderBy('name')->get()
                : collect();

            return view('scores.create', compact('teacherSubjects', 'selectedMapping', 'students'));
        })->name('scores.create');
    });
});

// ===== Web Admin CRUD Views (auth + admin only) =====
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:admin')->group(function () {

        Route::get('/app/admin', fn () => view('admin.dashboard'))->name('admin.dashboard');

        // Classrooms
        Route::get('/app/admin/classrooms', function () {
            $classrooms = Classroom::withCount('students')->orderBy('grade')->orderBy('name')->get();

            return view('admin.classrooms.index', compact('classrooms'));
        })->name('admin.classrooms.index');

        Route::get('/app/admin/classrooms/create', fn () => view('admin.classrooms.create'))->name('admin.classrooms.create');

        Route::post('/app/admin/classrooms', function (Request $req) {
            $data = $req->validate(['name' => 'required', 'grade' => 'required|in:X,XI,XII', 'academic_year' => 'required', 'description' => 'nullable']);
            Classroom::create($data);

            return redirect()->route('admin.classrooms.index')->with('success', 'Kelas berhasil ditambahkan');
        })->name('admin.classrooms.store');

        Route::get('/app/admin/classrooms/{classroom}/edit', fn (Classroom $classroom) => view('admin.classrooms.edit', compact('classroom')))->name('admin.classrooms.edit');

        Route::put('/app/admin/classrooms/{classroom}', function (Request $req, Classroom $classroom) {
            $data = $req->validate(['name' => 'required', 'grade' => 'required|in:X,XI,XII', 'academic_year' => 'required', 'description' => 'nullable']);
            $classroom->update($data);

            return redirect()->route('admin.classrooms.index')->with('success', 'Kelas berhasil diperbarui');
        })->name('admin.classrooms.update');

        Route::delete('/app/admin/classrooms/{classroom}', function (Classroom $classroom) {
            if ($classroom->students()->count() > 0) {
                return back()->withErrors(['Kelas masih memiliki siswa']);
            }
            $classroom->delete();

            return redirect()->route('admin.classrooms.index')->with('success', 'Kelas berhasil dihapus');
        })->name('admin.classrooms.destroy');

        // Subjects
        Route::get('/app/admin/subjects', function () {
            $subjects = Subject::withCount('teacherSubjects')->orderBy('name')->get();

            return view('admin.subjects.index', compact('subjects'));
        })->name('admin.subjects.index');

        Route::get('/app/admin/subjects/create', fn () => view('admin.subjects.create'))->name('admin.subjects.create');

        Route::post('/app/admin/subjects', function (Request $req) {
            $data = $req->validate(['name' => 'required', 'code' => 'required|unique:subjects,code', 'description' => 'nullable']);
            Subject::create($data);

            return redirect()->route('admin.subjects.index')->with('success', 'Mapel berhasil ditambahkan');
        })->name('admin.subjects.store');

        Route::get('/app/admin/subjects/{subject}/edit', fn (Subject $subject) => view('admin.subjects.edit', compact('subject')))->name('admin.subjects.edit');

        Route::put('/app/admin/subjects/{subject}', function (Request $req, Subject $subject) {
            $data = $req->validate(['name' => 'required', 'code' => 'required|unique:subjects,code,'.$subject->id, 'description' => 'nullable']);
            $subject->update($data);

            return redirect()->route('admin.subjects.index')->with('success', 'Mapel berhasil diperbarui');
        })->name('admin.subjects.update');

        Route::delete('/app/admin/subjects/{subject}', function (Subject $subject) {
            if ($subject->teacherSubjects()->count() > 0) {
                return back()->withErrors(['Mapel masih memiliki pengajar']);
            }
            $subject->delete();

            return redirect()->route('admin.subjects.index')->with('success', 'Mapel berhasil dihapus');
        })->name('admin.subjects.destroy');

        // Students
        Route::get('/app/admin/students', function () {
            $students = Student::with('classroom')->orderBy('name')->paginate(50);

            return view('admin.students.index', compact('students'));
        })->name('admin.students.index');

        Route::get('/app/admin/students/create', function () {
            $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();

            return view('admin.students.create', compact('classrooms'));
        })->name('admin.students.create');

        Route::post('/app/admin/students', function (Request $req) {
            $data = $req->validate([
                'classroom_id' => 'required|exists:classrooms,id',
                'nis' => 'required|unique:students,nis',
                'name' => 'required',
                'gender' => 'required|in:L,P',
                'birth_date' => 'nullable|date',
                'address' => 'nullable',
                'phone' => 'nullable',
                'parent_name' => 'nullable',
                'parent_phone' => 'nullable',
            ]);
            $user = User::create(['name' => $data['name'], 'email' => 'siswa'.$data['nis'].'@madani.id', 'password' => bcrypt('siswa123'), 'role' => 'wali_murid']);
            $data['user_id'] = $user->id;
            Student::create($data);

            return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil ditambahkan');
        })->name('admin.students.store');

        Route::get('/app/admin/students/{student}/edit', function (Student $student) {
            $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();

            return view('admin.students.edit', compact('student', 'classrooms'));
        })->name('admin.students.edit');

        Route::put('/app/admin/students/{student}', function (Request $req, Student $student) {
            $data = $req->validate([
                'classroom_id' => 'required|exists:classrooms,id',
                'nis' => 'required|unique:students,nis,'.$student->id,
                'name' => 'required',
                'gender' => 'required|in:L,P',
                'birth_date' => 'nullable|date',
                'address' => 'nullable',
                'phone' => 'nullable',
                'parent_name' => 'nullable',
                'parent_phone' => 'nullable',
            ]);
            $student->user->update(['name' => $data['name']]);
            $student->update($data);

            return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil diperbarui');
        })->name('admin.students.update');

        Route::delete('/app/admin/students/{student}', function (Student $student) {
            $student->user()->delete();
            $student->delete();

            return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus');
        })->name('admin.students.destroy');

        // Teacher-Subjects
        Route::get('/app/admin/teacher-subjects', function () {
            $mappings = TeacherSubject::with('user', 'subject', 'classroom')->orderBy('classroom_id')->get();

            return view('admin.teacher-subjects.index', compact('mappings'));
        })->name('admin.teacher-subjects.index');

        Route::get('/app/admin/teacher-subjects/create', function () {
            $teachers = User::where('role', 'guru')->orderBy('name')->get();
            $subjects = Subject::orderBy('name')->get();
            $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();

            return view('admin.teacher-subjects.create', compact('teachers', 'subjects', 'classrooms'));
        })->name('admin.teacher-subjects.create');

        Route::post('/app/admin/teacher-subjects', function (Request $req) {
            $data = $req->validate([
                'user_id' => 'required|exists:users,id',
                'subject_id' => 'required|exists:subjects,id',
                'classroom_id' => 'required|exists:classrooms,id',
            ]);
            TeacherSubject::firstOrCreate($data);

            return redirect()->route('admin.teacher-subjects.index')->with('success', 'Mapping berhasil ditambahkan');
        })->name('admin.teacher-subjects.store');

        Route::get('/app/admin/teacher-subjects/{teacher_subject}/edit', function (TeacherSubject $teacherSubject) {
            $teachers = User::where('role', 'guru')->orderBy('name')->get();
            $subjects = Subject::orderBy('name')->get();
            $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();

            return view('admin.teacher-subjects.edit', compact('teacherSubject', 'teachers', 'subjects', 'classrooms'));
        })->name('admin.teacher-subjects.edit');

        Route::put('/app/admin/teacher-subjects/{teacher_subject}', function (Request $req, TeacherSubject $teacherSubject) {
            $data = $req->validate([
                'user_id' => 'required|exists:users,id',
                'subject_id' => 'required|exists:subjects,id',
                'classroom_id' => 'required|exists:classrooms,id',
            ]);
            $teacherSubject->update($data);

            return redirect()->route('admin.teacher-subjects.index')->with('success', 'Mapping berhasil diperbarui');
        })->name('admin.teacher-subjects.update');

        Route::delete('/app/admin/teacher-subjects/{teacher_subject}', function (TeacherSubject $teacherSubject) {
            $teacherSubject->delete();

            return redirect()->route('admin.teacher-subjects.index')->with('success', 'Mapping berhasil dihapus');
        })->name('admin.teacher-subjects.destroy');

        // ===== Schedules =====
        Route::get('/app/admin/schedules', function () {
            $schedules = Schedule::with('teacherSubject.user', 'teacherSubject.subject', 'teacherSubject.classroom')
                ->orderBy('day')->orderBy('hour_order')->paginate(50);

            return view('admin.schedules.index', compact('schedules'));
        })->name('admin.schedules.index');

        Route::get('/app/admin/schedules/create', function () {
            $mappings = TeacherSubject::with('user', 'subject', 'classroom')->orderBy('classroom_id')->get();

            return view('admin.schedules.create', compact('mappings'));
        })->name('admin.schedules.create');

        Route::post('/app/admin/schedules', function (Request $req) {
            $data = $req->validate([
                'teacher_subject_id' => 'required|exists:teacher_subjects,id',
                'day' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
                'start_time' => 'required',
                'end_time' => 'required',
                'hour_order' => 'required|integer|min:1|max:4',
            ]);
            Schedule::create($data);

            return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil ditambahkan');
        })->name('admin.schedules.store');

        Route::get('/app/admin/schedules/{schedule}/edit', function (Schedule $schedule) {
            $mappings = TeacherSubject::with('user', 'subject', 'classroom')->orderBy('classroom_id')->get();

            return view('admin.schedules.edit', compact('schedule', 'mappings'));
        })->name('admin.schedules.edit');

        Route::put('/app/admin/schedules/{schedule}', function (Request $req, Schedule $schedule) {
            $data = $req->validate([
                'teacher_subject_id' => 'required|exists:teacher_subjects,id',
                'day' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
                'start_time' => 'required',
                'end_time' => 'required',
                'hour_order' => 'required|integer|min:1|max:4',
            ]);
            $schedule->update($data);

            return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui');
        })->name('admin.schedules.update');

        Route::delete('/app/admin/schedules/{schedule}', function (Schedule $schedule) {
            $schedule->delete();

            return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus');
        })->name('admin.schedules.destroy');

        // ===== Score Components =====
        Route::get('/app/admin/score-components', function () {
            $components = ScoreComponent::with('subject')->orderBy('subject_id')->get();

            return view('admin.score-components.index', compact('components'));
        })->name('admin.score-components.index');

        Route::get('/app/admin/score-components/create', function () {
            $subjects = Subject::orderBy('name')->get();

            return view('admin.score-components.create', compact('subjects'));
        })->name('admin.score-components.create');

        Route::post('/app/admin/score-components', function (Request $req) {
            $data = $req->validate([
                'subject_id' => 'required|exists:subjects,id',
                'code' => 'required|in:tugas,ph,uts,uas',
                'name' => 'required',
                'weight' => 'required|numeric|min:0|max:100',
                'semester' => 'required|in:ganjil,genap',
                'academic_year' => 'required',
            ]);
            ScoreComponent::create($data);

            return redirect()->route('admin.score-components.index')->with('success', 'Bobot nilai berhasil ditambahkan');
        })->name('admin.score-components.store');

        Route::get('/app/admin/score-components/{score_component}/edit', function (ScoreComponent $scoreComponent) {
            $subjects = Subject::orderBy('name')->get();

            return view('admin.score-components.edit', compact('scoreComponent', 'subjects'));
        })->name('admin.score-components.edit');

        Route::put('/app/admin/score-components/{score_component}', function (Request $req, ScoreComponent $scoreComponent) {
            $data = $req->validate([
                'subject_id' => 'required|exists:subjects,id',
                'code' => 'required|in:tugas,ph,uts,uas',
                'name' => 'required',
                'weight' => 'required|numeric|min:0|max:100',
                'semester' => 'required|in:ganjil,genap',
                'academic_year' => 'required',
            ]);
            $scoreComponent->update($data);

            return redirect()->route('admin.score-components.index')->with('success', 'Bobot nilai berhasil diperbarui');
        })->name('admin.score-components.update');

        Route::delete('/app/admin/score-components/{score_component}', function (ScoreComponent $scoreComponent) {
            $scoreComponent->delete();

            return redirect()->route('admin.score-components.index')->with('success', 'Bobot nilai berhasil dihapus');
        })->name('admin.score-components.destroy');

        // ===== Activity Logs =====
        Route::get('/app/admin/activity-logs', function () {
            $query = ActivityLog::with('user')->latest();

            if ($action = request('action')) {
                $query->where('action', $action);
            }

            if ($userId = request('user_id')) {
                $query->where('user_id', $userId);
            }

            $logs = $query->paginate(50);

            return view('admin.activity-logs.index', compact('logs'));
        })->name('admin.activity-logs.index');
    });
});
