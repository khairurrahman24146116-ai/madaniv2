<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ScoreComponentController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherSubjectController;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Schedule;
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

// ===== Halaman View (dengan middleware auth) =====
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/app/dashboard', function () {
        $schedules = Schedule::with('teacherSubject.subject', 'teacherSubject.classroom')
            ->when(! auth()->user()->isAdmin(), fn ($query) => $query->whereHas('teacherSubject', fn ($q) => $q->where('user_id', auth()->id())))
            ->get()
            ->unique(fn ($schedule) => implode('|', [$schedule->day, $schedule->start_time, $schedule->teacher_subject_id]));

        return view('dashboard', [
            'todaySessions' => $schedules->count(),
            'upcomingSchedules' => $schedules->take(3)->map(fn ($s) => [
                'time' => $s->start_time.' - '.$s->end_time,
                'subject' => $s->teacherSubject->subject->name ?? '',
                'class' => $s->teacherSubject->classroom->name ?? '',
                'type' => 'TEORI',
            ]),
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
        $student = Student::with('classroom')->first();

        return view('scores.rapor-preview', compact('student'));
    })->name('scores.rapor-preview');

    // ===== Guru & Admin: operasional mengajar =====
    Route::middleware('role:admin,guru')->group(function () {

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

// ===== API Auth Publik =====
Route::post('auth/login', [AuthController::class, 'login']);

// ===== API Auth Diperlukan (semua role) =====
Route::middleware('auth:sanctum')->group(function () {

    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    // ===== Admin & Guru =====
    Route::get('classrooms', [ClassroomController::class, 'index']);
    Route::get('classrooms/{classroom}', [ClassroomController::class, 'show']);
    Route::get('classrooms/{classroom}/students', [ClassroomController::class, 'getActiveStudents']);
    Route::get('subjects', [SubjectController::class, 'index']);
    Route::get('subjects/{subject}', [SubjectController::class, 'show']);
    Route::get('students', [StudentController::class, 'index']);
    Route::get('students/{student}', [StudentController::class, 'show']);
    Route::get('schedules/day', [ScheduleController::class, 'getDaySchedule']);
    Route::get('schedules', [ScheduleController::class, 'index']);
    Route::get('schedules/{schedule}', [ScheduleController::class, 'show']);
    Route::get('teacher-subjects', [TeacherSubjectController::class, 'index']);
    Route::get('teacher-subjects/{teacher_subject}', [TeacherSubjectController::class, 'show']);
    Route::get('teacher-subjects/schedule', [TeacherSubjectController::class, 'getTeacherSchedule']);

    // ===== Read: semua role =====
    Route::get('attendances', [AttendanceController::class, 'index']);
    Route::get('scores', [ScoreController::class, 'index']);
    Route::get('scores/rapor', [ScoreController::class, 'rapor']);
    Route::get('scores/rapor-pdf', [ScoreController::class, 'raporPdf']);

    // ===== Guru & Admin: write attendance & scores =====
    Route::middleware('role:admin,guru')->group(function () {

        Route::get('attendances/form', [AttendanceController::class, 'form'])->name('api.attendances.form');
        Route::get('attendances/export-csv', [AttendanceController::class, 'exportCsv'])->name('attendances.export-csv');
        Route::post('attendances', [AttendanceController::class, 'store']);
        Route::put('attendances/{attendance}', [AttendanceController::class, 'update']);
        Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy']);
        Route::get('attendances/{attendance}', [AttendanceController::class, 'show']);

        Route::get('scores/export-csv', [ScoreController::class, 'exportCsv'])->name('scores.export-csv');
        Route::get('scores/final-grade', [ScoreController::class, 'finalGrade']);
        Route::get('scores/batch-final-grade', [ScoreController::class, 'batchFinalGrade']);
        Route::post('scores', [ScoreController::class, 'store']);
        Route::post('scores/batch', [ScoreController::class, 'batchStore']);
        Route::put('scores/{score}', [ScoreController::class, 'update']);
        Route::delete('scores/{score}', [ScoreController::class, 'destroy']);
        Route::get('scores/{score}', [ScoreController::class, 'show']);
    });

    // ===== Admin Only =====
    Route::middleware('role:admin')->group(function () {
        Route::post('classrooms', [ClassroomController::class, 'store']);
        Route::put('classrooms/{classroom}', [ClassroomController::class, 'update']);
        Route::delete('classrooms/{classroom}', [ClassroomController::class, 'destroy']);

        Route::post('students', [StudentController::class, 'store']);
        Route::post('students/bulk-import', [StudentController::class, 'bulkImport']);
        Route::put('students/{student}', [StudentController::class, 'update']);
        Route::delete('students/{student}', [StudentController::class, 'destroy']);

        Route::post('subjects', [SubjectController::class, 'store']);
        Route::put('subjects/{subject}', [SubjectController::class, 'update']);
        Route::delete('subjects/{subject}', [SubjectController::class, 'destroy']);

        Route::post('teacher-subjects', [TeacherSubjectController::class, 'store']);
        Route::delete('teacher-subjects/{teacher_subject}', [TeacherSubjectController::class, 'destroy']);

        Route::post('schedules', [ScheduleController::class, 'store']);
        Route::put('schedules/{schedule}', [ScheduleController::class, 'update']);
        Route::delete('schedules/{schedule}', [ScheduleController::class, 'destroy']);

        Route::apiResource('score-components', ScoreComponentController::class);

        // ===== Web Admin CRUD Views =====
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

        Route::delete('/app/admin/teacher-subjects/{teacher_subject}', function (TeacherSubject $teacherSubject) {
            $teacherSubject->delete();

            return redirect()->route('admin.teacher-subjects.index')->with('success', 'Mapping berhasil dihapus');
        })->name('admin.teacher-subjects.destroy');
    });
});
