<?php

use App\Http\Controllers\ActiveLetterController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\SPPController;
use App\Http\Controllers\StudentImportExportController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Requests\MoveStudentRequest;
use App\Http\Requests\ResetUserPasswordRequest;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\StoreScoreComponentRequest;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\StoreTeacherSubjectRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Requests\UpdateScoreComponentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Requests\UpdateTeacherSubjectRequest;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Score;
use App\Models\ScoreComponent;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeacherAttendance;
use App\Models\TeacherSubject;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\RaporService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    if (auth()->check()) {
        $redirect = auth()->user()->isWaliMurid() ? 'wali-murid.dashboard' : (auth()->user()->isAdmin() ? 'admin.dashboard' : 'dashboard');

        return redirect()->route($redirect);
    }

    return view('welcome');
});

// ===== Web Login =====
Route::post('/auth/login/web', [AuthController::class, 'loginWeb'])->name('auth.login.web')->middleware('throttle:5,1');
Route::post('/auth/logout/web', [AuthController::class, 'logoutWeb'])->name('auth.logout.web');

// ===== Ganti Password Wajib (must_change_password) =====
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/app/ganti-password', [PasswordChangeController::class, 'showForm'])->name('password.change');
    Route::post('/app/ganti-password', [PasswordChangeController::class, 'update'])->name('password.change.update');
});

Route::get('/login', function () {
    if (auth()->check()) {
        $redirect = auth()->user()->isWaliMurid() ? 'wali-murid.dashboard' : (auth()->user()->isAdmin() ? 'admin.dashboard' : 'dashboard');

        return redirect()->route($redirect);
    }

    return view('welcome');
})->name('login');

// ===== Password Reset =====
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [PasswordResetController::class, 'forgotPassword'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:3,1');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'updatePassword'])->name('password.update')->middleware('throttle:5,1');
});

// ===== Halaman View (dengan middleware auth) =====
Route::middleware(['auth:sanctum', 'password.changed'])->group(function () {

    // ===== Profile (semua user) =====
    Route::get('/app/profile', function () {
        return view('profile.edit');
    })->name('profile.edit');

    Route::post('/app/profile', function (UpdateProfileRequest $req) {
        $data = $req->validated();

        $user = auth()->user();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->address = $data['address'] ?? null;

        if ($req->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $req->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        if ($req->boolean('remove_photo') && $user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
        }

        $user->save();

        ActivityLogger::log('update', 'Memperbarui profil: '.$user->name);

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    })->name('profile.update');

    // ===== Wali Murid: Dashboard & Rapor (tidak di-nesting di dalam admin,guru) =====
    Route::middleware('role:wali_murid')->group(function () {
        Route::get('/app/wali-murid', function () {
            $students = Student::with([
                'classroom',
                'attendances as hadir_count' => fn ($q) => $q->where('status', 'H'),
                'attendances as tidak_hadir_count' => fn ($q) => $q->whereNot('status', 'H'),
            ])->where('user_id', auth()->id())->get();

            return view('wali-murid.dashboard', compact('students'));
        })->name('wali-murid.dashboard');

        Route::get('/app/wali-murid/surat', [LetterController::class, 'waliIndex'])->name('wali.letters.index');
        Route::get('/app/wali-murid/surat/{letter}', [LetterController::class, 'show'])->name('wali.letters.show');

        Route::get('/app/wali-murid/kontak', [ContactController::class, 'waliIndex'])->name('wali.contact.index');
        Route::get('/app/wali-murid/kontak/buat', [ContactController::class, 'waliCreate'])->name('wali.contact.create');
        Route::post('/app/wali-murid/kontak', [ContactController::class, 'waliStore'])->name('wali.contact.store');

        Route::get('/app/wali-murid/pertemuan', [MeetingController::class, 'waliIndex'])->name('wali.meetings.index');
        Route::get('/app/wali-murid/pertemuan/buat', [MeetingController::class, 'waliCreate'])->name('wali.meetings.create');
        Route::post('/app/wali-murid/pertemuan', [MeetingController::class, 'waliStore'])->name('wali.meetings.store');

        Route::get('/app/wali-murid/rapor/{student}', function (Student $student) {
            if ($student->user_id !== auth()->id()) {
                abort(403);
            }

            $student->load('classroom');

            $semester = request('semester', 'ganjil');
            $academicYear = request('academic_year', '2025/2026');
            $raporService = app(RaporService::class);
            $subjects = Subject::get();
            $grades = $raporService->calculateGrades([$student->id], $subjects->pluck('id')->all(), $semester, $academicYear);
            $rapor = collect();

            foreach ($subjects as $subject) {
                $result = $grades[$student->id][$subject->id] ?? null;

                if (! $result || $result['final_grade'] === null) {
                    continue;
                }

                $componentData = collect($result['components'])->map(fn ($comp) => [
                    'name' => $comp['name'],
                    'value' => $comp['average_score'],
                    'weight' => $comp['weight'],
                    'weighted' => $comp['weighted_score'],
                ])->values()->all();

                $rapor->push([
                    'subject' => $subject->name,
                    'final_grade' => $result['final_grade'],
                    'components' => $componentData,
                ]);
            }

            return view('wali-murid.rapor', compact('student', 'rapor'));
        })->name('wali-murid.rapor');
    });

    // ===== Guru: dashboard dan operasional mengajar =====
    Route::middleware('role:guru')->group(function () {

        Route::get('/app/guru/surat', [LetterController::class, 'guruIndex'])->name('guru.letters.index');
        Route::get('/app/guru/surat/{letter}', [LetterController::class, 'show'])->name('guru.letters.show');

        Route::get('/app/teacher-attendances/form', function () {
            $schedules = Schedule::with('teacherSubject.subject', 'teacherSubject.classroom')
                ->when(! auth()->user()->isAdmin(), fn ($query) => $query->whereHas('teacherSubject', fn ($q) => $q->where('user_id', auth()->id())))
                ->get()
                ->unique(fn ($schedule) => implode('|', [$schedule->day, $schedule->start_time, $schedule->teacher_subject_id]));
            $todayAttendance = TeacherAttendance::where('user_id', auth()->id())
                ->where('date', now()->format('Y-m-d'))
                ->first();

            return view('teacher-attendances.form', compact('schedules', 'todayAttendance'));
        })->name('teacher.attendances.form');

        Route::post('/app/teacher-attendances/check-in', [TeacherAttendanceController::class, 'checkIn'])->name('teacher.attendances.checkin');
        Route::post('/app/teacher-attendances/check-out', [TeacherAttendanceController::class, 'checkOut'])->name('teacher.attendances.checkout');
        Route::post('/app/teacher-attendances', [TeacherAttendanceController::class, 'store'])->name('teacher.attendances.store');

        Route::get('/app/teacher-attendances', function () {
            $attendances = TeacherAttendance::with('user', 'schedule.teacherSubject.subject')
                ->where('user_id', auth()->id())
                ->orderBy('date', 'desc')
                ->paginate(50);

            return view('teacher-attendances.index', compact('attendances'));
        })->name('teacher.attendances.index');

        Route::get('/app/dashboard', function () {
            $user = auth()->user();

            TeacherAttendanceController::autoAttendIfWithinSoreHours($user);

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

            Gate::authorize('view', $student);
            $semester = request('semester', 'ganjil');
            $academicYear = request('academic_year', '2025/2026');

            $grades = collect();
            if ($student) {
                $raporService = app(RaporService::class);
                $subjects = Subject::get();
                $bulkGrades = $raporService->calculateGrades([$student->id], $subjects->pluck('id')->all(), $semester, $academicYear);

                foreach ($subjects as $subject) {
                    $result = $bulkGrades[$student->id][$subject->id] ?? null;

                    if (! $result || $result['final_grade'] === null) {
                        continue;
                    }

                    $grades->push([
                        'subject' => $subject->name,
                        'score' => $result['final_grade'],
                        'kkm' => 70,
                    ]);
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
            $user = auth()->user();

            TeacherAttendanceController::autoAttendIfWithinSoreHours($user);

            $schedules = Schedule::with('teacherSubject.subject', 'teacherSubject.classroom')
                ->when(! $user->isAdmin(), fn ($query) => $query->whereHas('teacherSubject', fn ($q) => $q->where('user_id', $user->id)))
                ->get()
                ->unique(fn ($schedule) => implode('|', [$schedule->day, $schedule->start_time, $schedule->teacher_subject_id]));
            $scheduleId = request('schedule_id', $schedules->first()?->id);
            $date = request('date', now()->format('Y-m-d'));
            $schedule = $schedules->firstWhere('id', $scheduleId);
            $students = $schedule?->teacherSubject->classroom->students ?? collect();
            $canEdit = $user->isAdmin() || AttendanceController::isWithinSoreHours();

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
        Route::get('/app/admin/classrooms', [ClassroomController::class, 'webIndex'])->name('admin.classrooms.index');
        Route::get('/app/admin/classrooms/create', [ClassroomController::class, 'webCreate'])->name('admin.classrooms.create');
        Route::post('/app/admin/classrooms', [ClassroomController::class, 'webStore'])->name('admin.classrooms.store');
        Route::get('/app/admin/classrooms/{classroom}/edit', [ClassroomController::class, 'webEdit'])->name('admin.classrooms.edit');
        Route::put('/app/admin/classrooms/{classroom}', [ClassroomController::class, 'webUpdate'])->name('admin.classrooms.update');
        Route::delete('/app/admin/classrooms/{classroom}', [ClassroomController::class, 'webDestroy'])->name('admin.classrooms.destroy');

        // Subjects
        Route::get('/app/admin/subjects', [SubjectController::class, 'webIndex'])->name('admin.subjects.index');
        Route::get('/app/admin/subjects/create', [SubjectController::class, 'webCreate'])->name('admin.subjects.create');
        Route::post('/app/admin/subjects', [SubjectController::class, 'webStore'])->name('admin.subjects.store');
        Route::get('/app/admin/subjects/{subject}/edit', [SubjectController::class, 'webEdit'])->name('admin.subjects.edit');
        Route::put('/app/admin/subjects/{subject}', [SubjectController::class, 'webUpdate'])->name('admin.subjects.update');
        Route::delete('/app/admin/subjects/{subject}', [SubjectController::class, 'webDestroy'])->name('admin.subjects.destroy');

        // Students
        Route::get('/app/admin/students', function (Request $req) {
            $query = Student::with('classroom');

            if ($search = $req->get('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "{$search}%");
                });
            }

            if ($req->has('status') && $req->get('status') !== '') {
                $query->where('is_active', $req->boolean('status'));
            }

            $students = $query->orderBy('name')->paginate(50)->withQueryString();

            return view('admin.students.index', compact('students'));
        })->name('admin.students.index');

        Route::get('/app/admin/students/create', function () {
            $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();

            return view('admin.students.create', compact('classrooms'));
        })->name('admin.students.create');

        Route::post('/app/admin/students', function (StoreStudentRequest $req) {
            $data = $req->validated();
            DB::transaction(function () use (&$data) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => 'siswa'.$data['nis'].'@madani.id',
                    'password' => Str::random(10),
                    'role' => 'wali_murid',
                    'must_change_password' => true,
                ]);
                $data['user_id'] = $user->id;
                Student::create($data);
            });
            ActivityLogger::log('create', 'Menambahkan siswa: '.$data['name'].' (NIS: '.$data['nis'].')');

            return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil ditambahkan');
        })->name('admin.students.store');

        Route::get('/app/admin/students/{student}/edit', function (Student $student) {
            $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();

            return view('admin.students.edit', compact('student', 'classrooms'));
        })->name('admin.students.edit');

        Route::put('/app/admin/students/{student}', function (UpdateStudentRequest $req, Student $student) {
            $data = $req->validated();
            $student->user->update(['name' => $data['name']]);
            $student->update($data);
            ActivityLogger::log('update', 'Mengubah siswa: '.$data['name'].' (NIS: '.$data['nis'].')', $student);

            return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil diperbarui');
        })->name('admin.students.update');

        Route::delete('/app/admin/students/{student}', function (Student $student) {
            $name = $student->name;
            $nis = $student->nis;
            $student->user()->delete();
            $student->delete();
            ActivityLogger::log('delete', 'Menghapus siswa: '.$name.' (NIS: '.$nis.')');

            return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus');
        })->name('admin.students.destroy');

        // Pindah kelas
        Route::get('/app/admin/students/{student}/move', function (Student $student) {
            $classrooms = Classroom::where('id', '!=', $student->classroom_id)->orderBy('grade')->orderBy('name')->get();

            return view('admin.students.move', compact('student', 'classrooms'));
        })->name('admin.students.move-form');

        Route::post('/app/admin/students/{student}/move', function (MoveStudentRequest $req, Student $student) {
            $data = $req->validated();
            $oldClassroom = $student->classroom->name;
            $student->update(['classroom_id' => $data['classroom_id']]);
            ActivityLogger::log('update', "Memindahkan siswa {$student->name} (NIS: {$student->nis}) dari {$oldClassroom} ke kelas baru");

            return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dipindahkan');
        })->name('admin.students.move');

        // Import & Export Excel
        Route::get('/app/admin/students/import', [StudentImportExportController::class, 'importForm'])->name('admin.students.import-form');
        Route::post('/app/admin/students/import', [StudentImportExportController::class, 'import'])->name('admin.students.import');
        Route::get('/app/admin/students/export', [StudentImportExportController::class, 'export'])->name('admin.students.export');

        // Teacher-Subjects
        Route::get('/app/admin/teacher-subjects', function () {
            $mappings = TeacherSubject::with('user', 'subject', 'classroom')->withCount('schedules')->orderBy('classroom_id')->paginate(50);

            return view('admin.teacher-subjects.index', compact('mappings'));
        })->name('admin.teacher-subjects.index');

        Route::get('/app/admin/teacher-subjects/create', function () {
            $teachers = User::where('role', 'guru')->orderBy('name')->get();
            $subjects = Subject::orderBy('name')->get();
            $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();

            return view('admin.teacher-subjects.create', compact('teachers', 'subjects', 'classrooms'));
        })->name('admin.teacher-subjects.create');

        Route::post('/app/admin/teacher-subjects', function (StoreTeacherSubjectRequest $req) {
            $data = $req->validated();
            TeacherSubject::firstOrCreate($data);
            ActivityLogger::log('create', 'Menambahkan mapping guru-mapel-kelas');

            return redirect()->route('admin.teacher-subjects.index')->with('success', 'Mapping berhasil ditambahkan');
        })->name('admin.teacher-subjects.store');

        Route::get('/app/admin/teacher-subjects/{teacher_subject}/edit', function (TeacherSubject $teacherSubject) {
            $teachers = User::where('role', 'guru')->orderBy('name')->get();
            $subjects = Subject::orderBy('name')->get();
            $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();

            return view('admin.teacher-subjects.edit', compact('teacherSubject', 'teachers', 'subjects', 'classrooms'));
        })->name('admin.teacher-subjects.edit');

        Route::put('/app/admin/teacher-subjects/{teacher_subject}', function (UpdateTeacherSubjectRequest $req, TeacherSubject $teacherSubject) {
            $data = $req->validated();
            $teacherSubject->update($data);
            ActivityLogger::log('update', 'Mengubah mapping guru-mapel-kelas', $teacherSubject);

            return redirect()->route('admin.teacher-subjects.index')->with('success', 'Mapping berhasil diperbarui');
        })->name('admin.teacher-subjects.update');

        Route::delete('/app/admin/teacher-subjects/{teacher_subject}', function (TeacherSubject $teacherSubject) {
            $teacherSubject->delete();
            ActivityLogger::log('delete', 'Menghapus mapping guru-mapel-kelas');

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

        Route::post('/app/admin/schedules', function (StoreScheduleRequest $req) {
            $data = $req->validated();
            Schedule::create($data);
            ActivityLogger::log('create', 'Menambahkan jadwal: '.ucfirst($data['day']).' jam '.$data['start_time']);

            return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil ditambahkan');
        })->name('admin.schedules.store');

        Route::get('/app/admin/schedules/{schedule}/edit', function (Schedule $schedule) {
            $mappings = TeacherSubject::with('user', 'subject', 'classroom')->orderBy('classroom_id')->get();

            return view('admin.schedules.edit', compact('schedule', 'mappings'));
        })->name('admin.schedules.edit');

        Route::put('/app/admin/schedules/{schedule}', function (UpdateScheduleRequest $req, Schedule $schedule) {
            $data = $req->validated();
            $schedule->update($data);
            ActivityLogger::log('update', 'Mengubah jadwal: '.ucfirst($data['day']).' jam '.$data['start_time'], $schedule);

            return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui');
        })->name('admin.schedules.update');

        Route::delete('/app/admin/schedules/{schedule}', function (Schedule $schedule) {
            $day = $schedule->day;
            $time = $schedule->start_time;
            $schedule->delete();
            ActivityLogger::log('delete', 'Menghapus jadwal: '.ucfirst($day).' jam '.$time);

            return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus');
        })->name('admin.schedules.destroy');

        // ===== Score Components =====
        Route::get('/app/admin/score-components', function () {
            $components = ScoreComponent::with('subject')->orderBy('subject_id')->paginate(50);

            return view('admin.score-components.index', compact('components'));
        })->name('admin.score-components.index');

        Route::get('/app/admin/score-components/create', function () {
            $subjects = Subject::orderBy('name')->get();

            return view('admin.score-components.create', compact('subjects'));
        })->name('admin.score-components.create');

        Route::post('/app/admin/score-components', function (StoreScoreComponentRequest $req) {
            $data = $req->validated();
            ScoreComponent::create($data);
            ActivityLogger::log('create', 'Menambahkan bobot nilai: '.$data['name'].' ('.$data['code'].')');

            return redirect()->route('admin.score-components.index')->with('success', 'Bobot nilai berhasil ditambahkan');
        })->name('admin.score-components.store');

        Route::get('/app/admin/score-components/{score_component}/edit', function (ScoreComponent $scoreComponent) {
            $subjects = Subject::orderBy('name')->get();

            return view('admin.score-components.edit', compact('scoreComponent', 'subjects'));
        })->name('admin.score-components.edit');

        Route::put('/app/admin/score-components/{score_component}', function (UpdateScoreComponentRequest $req, ScoreComponent $scoreComponent) {
            $data = $req->validated();
            $scoreComponent->update($data);
            ActivityLogger::log('update', 'Mengubah bobot nilai: '.$data['name'], $scoreComponent);

            return redirect()->route('admin.score-components.index')->with('success', 'Bobot nilai berhasil diperbarui');
        })->name('admin.score-components.update');

        Route::delete('/app/admin/score-components/{score_component}', function (ScoreComponent $scoreComponent) {
            $name = $scoreComponent->name;
            $scoreComponent->delete();
            ActivityLogger::log('delete', 'Menghapus bobot nilai: '.$name);

            return redirect()->route('admin.score-components.index')->with('success', 'Bobot nilai berhasil dihapus');
        })->name('admin.score-components.destroy');

        // ===== Teacher Attendance (Admin) =====
        Route::get('/app/admin/teacher-attendances', function () {
            $today = now()->format('Y-m-d');
            $date = request('date', $today);

            $query = TeacherAttendance::with('user', 'schedule.teacherSubject.subject')
                ->when(request('date'), fn ($q) => $q->where('date', request('date')))
                ->when(request('user_id'), fn ($q) => $q->where('user_id', request('user_id')))
                ->when(request('status'), fn ($q) => $q->where('status', request('status')));

            $attendances = $query->orderBy('date', 'desc')->orderBy('check_in', 'desc')->paginate(50);

            $gurus = User::where('role', 'guru')->orderBy('name')->get();

            $todayAttendances = TeacherAttendance::where('date', $today)->select('status')->get();
            $totalGuru = $gurus->count();
            $hadir = $todayAttendances->where('status', 'H')->count();
            $sakit = $todayAttendances->where('status', 'S')->count();
            $izin = $todayAttendances->where('status', 'I')->count();
            $alpa = $todayAttendances->where('status', 'A')->count();
            $belumAbsen = $totalGuru - $todayAttendances->count();

            return view('admin.teacher-attendances.index', compact('attendances', 'gurus', 'today', 'totalGuru', 'hadir', 'sakit', 'izin', 'alpa', 'belumAbsen'));
        })->name('admin.teacher-attendances.index');

        // ===== Users Management =====
        Route::get('/app/admin/users', function () {
            $query = User::query();

            if ($role = request('role')) {
                $query->where('role', $role);
            }

            if ($search = request('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $users = $query->orderBy('role')->orderBy('name')->paginate(50);

            $totalUsers = User::count();
            $totalGuru = User::where('role', 'guru')->count();
            $totalWaliMurid = User::where('role', 'wali_murid')->count();
            $totalAdmin = User::where('role', 'admin')->count();

            return view('admin.users.index', compact('users', 'totalUsers', 'totalGuru', 'totalWaliMurid', 'totalAdmin'));
        })->name('admin.users.index');

        Route::post('/app/admin/users/{user}/reset-password', function (ResetUserPasswordRequest $req, User $user) {
            $data = $req->validated();

            $plainPassword = $data['password'];
            $user->update([
                'password' => bcrypt($plainPassword),
                'must_change_password' => true,
            ]);

            ActivityLogger::log('update', 'Reset password: '.$user->name.' ('.$user->email.')');

            // Simpan password SEKALI pakai saja (single-use). Hilang setelah ditampilkan.
            session()->put('pending_reset_password', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'password' => $plainPassword,
            ]);

            return redirect()->route('admin.users.password-reveal', $user);
        })->name('admin.users.reset-password')->middleware('throttle:10,1');

        // Halaman khusus yang menampilkan password SEKALI setelah reset.
        // Password diambil dari session single-use lalu dihapus, sehingga
        // refresh/back/tebus tidak akan menampilkan lagi.
        Route::get('/app/admin/users/{user}/password-reveal', function (User $user) {
            $pending = session()->pull('pending_reset_password');

            if ($pending === null || (int) $pending['user_id'] !== (int) $user->id) {
                return redirect()->route('admin.users.index');
            }

            return view('admin.users.password-reveal', [
                'userName' => $pending['user_name'],
                'password' => $pending['password'],
            ]);
        })->name('admin.users.password-reveal')->middleware('throttle:10,1');

        // ===== Surat =====
        Route::get('/app/admin/surat', [LetterController::class, 'adminIndex'])->name('admin.letters.index');
        Route::get('/app/admin/surat/buat', [LetterController::class, 'create'])->name('admin.letters.create');
        Route::post('/app/admin/surat', [LetterController::class, 'store'])->name('admin.letters.store');
        Route::get('/app/admin/surat/{letter}/edit', [LetterController::class, 'edit'])->name('admin.letters.edit');
        Route::put('/app/admin/surat/{letter}', [LetterController::class, 'update'])->name('admin.letters.update');
        Route::delete('/app/admin/surat/{letter}', [LetterController::class, 'destroy'])->name('admin.letters.destroy');

        // ===== Pesan Masuk =====
        Route::get('/app/admin/pesan', [ContactController::class, 'adminIndex'])->name('admin.contact.index');
        Route::get('/app/admin/pesan/{contactMessage}', [ContactController::class, 'adminShow'])->name('admin.contact.show');
        Route::post('/app/admin/pesan/{contactMessage}/balas', [ContactController::class, 'adminReply'])->name('admin.contact.reply');

        // ===== Pertemuan =====
        Route::get('/app/admin/pertemuan', [MeetingController::class, 'adminIndex'])->name('admin.meetings.index');
        Route::get('/app/admin/pertemuan/{meeting}', [MeetingController::class, 'adminShow'])->name('admin.meetings.show');
        Route::post('/app/admin/pertemuan/{meeting}/setujui', [MeetingController::class, 'adminApprove'])->name('admin.meetings.approve');
        Route::post('/app/admin/pertemuan/{meeting}/tolak', [MeetingController::class, 'adminReject'])->name('admin.meetings.reject');

        // ===== Activity Logs =====
        Route::get('/app/admin/activity-logs', function () {
            $query = ActivityLog::with('user')
                ->select(['id', 'user_id', 'action', 'description', 'ip_address', 'created_at'])
                ->latest();

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

    // ===== PDF Rapor (semua role) =====
    Route::get('/app/rapor-pdf', [ScoreController::class, 'raporPdf'])->name('rapor.pdf');

    // ===== Cetak Surat (semua role) =====
    Route::get('/app/surat/cetak/{letter}', [LetterController::class, 'printPdf'])->name('letters.print');

    // ===== SPP (semua role) =====
    Route::get('/app/spp', [SPPController::class, 'index'])->name('spp.index');

    // ===== SPP: ubah status bayar (khusus admin) =====
    Route::middleware('role:admin')->group(function () {
        Route::post('/app/spp/bayar', [SPPController::class, 'markPaid'])->name('spp.mark-paid');
        Route::post('/app/spp/{studentFee}/batal', [SPPController::class, 'markUnpaid'])->name('spp.mark-unpaid');
    });

    // ===== Surat Aktif Siswa (semua role) =====
    Route::get('/app/active-letters', [ActiveLetterController::class, 'index'])->name('active-letters.index');
    Route::get('/app/active-letters/buat', [ActiveLetterController::class, 'create'])->name('active-letters.create');
    Route::post('/app/active-letters', [ActiveLetterController::class, 'store'])->name('active-letters.store');
    Route::get('/app/active-letters/{activeLetter}', [ActiveLetterController::class, 'show'])->name('active-letters.show');
    Route::post('/app/active-letters/{activeLetter}/ambil', [ActiveLetterController::class, 'markTaken'])->name('active-letters.mark-taken');
    Route::get('/app/active-letters/cetak/{activeLetter}', [ActiveLetterController::class, 'printPdf'])->name('active-letters.print');
});
