<?php

use App\Http\Controllers\ActiveLetterController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\SPPController;
use App\Http\Controllers\StudentImportExportController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Requests\MoveStudentRequest;
use App\Http\Requests\ResetUserPasswordRequest;
use App\Http\Requests\StoreClassroomRequest;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\StoreScoreComponentRequest;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\StoreTeacherSubjectRequest;
use App\Http\Requests\UpdateClassroomRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Requests\UpdateScoreComponentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Requests\UpdateSubjectRequest;
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
Route::post('/auth/login/web', [AuthController::class, 'loginWeb'])->name('auth.login.web');
Route::post('/auth/logout/web', [AuthController::class, 'logoutWeb'])->name('auth.logout.web');

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
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'updatePassword'])->name('password.update');
});

// ===== Halaman View (dengan middleware auth) =====
Route::middleware('auth:sanctum')->group(function () {

    // ===== Profile (semua user) =====
    Route::get('/app/profile', function () {
        return view('profile.edit');
    })->name('profile.edit');

    Route::post('/app/profile', function (Request $req) {
        $data = $req->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.auth()->id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

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
            $students = Student::with('classroom')->where('user_id', auth()->id())->get();

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

            if (! $user->isAdmin() && AttendanceController::isWithinSoreHours()) {
                $today = now()->format('Y-m-d');
                $existing = TeacherAttendance::where('user_id', $user->id)
                    ->where('date', $today)
                    ->first();

                if (! $existing || ! $existing->check_in) {
                    TeacherAttendanceController::autoAttend($user->id);
                }
            }

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
            $user = auth()->user();

            if (! $user->isAdmin() && AttendanceController::isWithinSoreHours()) {
                $today = now()->format('Y-m-d');
                $existing = TeacherAttendance::where('user_id', $user->id)
                    ->where('date', $today)
                    ->first();

                if (! $existing || ! $existing->check_in) {
                    TeacherAttendanceController::autoAttend($user->id);
                }
            }

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
        Route::get('/app/admin/classrooms', function () {
            $classrooms = Classroom::with('waliKelas')->withCount('students')->orderBy('grade')->orderBy('name')->get();

            return view('admin.classrooms.index', compact('classrooms'));
        })->name('admin.classrooms.index');

        Route::get('/app/admin/classrooms/create', function () {
            $gurus = User::where('role', 'guru')->orderBy('name')->get();

            return view('admin.classrooms.create', compact('gurus'));
        })->name('admin.classrooms.create');

        Route::post('/app/admin/classrooms', function (Request $req) {
            $data = $req->validate(['name' => 'required', 'grade' => 'required|in:X,XI,XII', 'academic_year' => 'required', 'description' => 'nullable', 'wali_kelas_id' => 'nullable|exists:users,id']);
            Classroom::create($data);
            ActivityLogger::log('create', 'Menambahkan kelas: '.$data['name']);

            return redirect()->route('admin.classrooms.index')->with('success', 'Kelas berhasil ditambahkan');
        })->name('admin.classrooms.store');

        Route::get('/app/admin/classrooms/{classroom}/edit', function (Classroom $classroom) {
            $gurus = User::where('role', 'guru')->orderBy('name')->get();

            return view('admin.classrooms.edit', compact('classroom', 'gurus'));
        })->name('admin.classrooms.edit');

        Route::put('/app/admin/classrooms/{classroom}', function (Request $req, Classroom $classroom) {
            $data = $req->validate(['name' => 'required', 'grade' => 'required|in:X,XI,XII', 'academic_year' => 'required', 'description' => 'nullable', 'wali_kelas_id' => 'nullable|exists:users,id']);
            $classroom->update($data);
            ActivityLogger::log('update', 'Mengubah kelas: '.$data['name'], $classroom);

            return redirect()->route('admin.classrooms.index')->with('success', 'Kelas berhasil diperbarui');
        })->name('admin.classrooms.update');

        Route::delete('/app/admin/classrooms/{classroom}', function (Classroom $classroom) {
            if ($classroom->students()->count() > 0) {
                return back()->withErrors(['Kelas masih memiliki siswa']);
            }
            $name = $classroom->name;
            $classroom->delete();
            ActivityLogger::log('delete', 'Menghapus kelas: '.$name);

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
            ActivityLogger::log('create', 'Menambahkan mapel: '.$data['name'].' ('.$data['code'].')');

            return redirect()->route('admin.subjects.index')->with('success', 'Mapel berhasil ditambahkan');
        })->name('admin.subjects.store');

        Route::get('/app/admin/subjects/{subject}/edit', fn (Subject $subject) => view('admin.subjects.edit', compact('subject')))->name('admin.subjects.edit');

        Route::put('/app/admin/subjects/{subject}', function (Request $req, Subject $subject) {
            $data = $req->validate(['name' => 'required', 'code' => 'required|unique:subjects,code,'.$subject->id, 'description' => 'nullable']);
            $subject->update($data);
            ActivityLogger::log('update', 'Mengubah mapel: '.$data['name'], $subject);

            return redirect()->route('admin.subjects.index')->with('success', 'Mapel berhasil diperbarui');
        })->name('admin.subjects.update');

        Route::delete('/app/admin/subjects/{subject}', function (Subject $subject) {
            if ($subject->teacherSubjects()->count() > 0) {
                return back()->withErrors(['Mapel masih memiliki pengajar']);
            }
            $name = $subject->name;
            $subject->delete();
            ActivityLogger::log('delete', 'Menghapus mapel: '.$name);

            return redirect()->route('admin.subjects.index')->with('success', 'Mapel berhasil dihapus');
        })->name('admin.subjects.destroy');

        // Students
        Route::get('/app/admin/students', function (Request $req) {
            $query = Student::with('classroom');

            if ($search = $req->get('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
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
            ActivityLogger::log('create', 'Menambahkan siswa: '.$data['name'].' (NIS: '.$data['nis'].')');

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

        Route::post('/app/admin/students/{student}/move', function (Request $req, Student $student) {
            $data = $req->validate(['classroom_id' => 'required|exists:classrooms,id']);
            $oldClassroom = $student->classroom->name;
            $student->update(['classroom_id' => $data['classroom_id']]);
            ActivityLogger::log('update', "Memindahkan siswa {$student->name} (NIS: {$student->nis}) dari {$oldClassroom} ke kelas baru");

            return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dipindahkan');
        })->name('admin.students.move');

        // Import Excel
        Route::get('/app/admin/students/import', function () {
            $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();

            return view('admin.students.import', compact('classrooms'));
        })->name('admin.students.import-form');

        Route::post('/app/admin/students/import', function (Request $req) {
            $data = $req->validate([
                'classroom_id' => 'required|exists:classrooms,id',
                'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            ]);

            $classroomId = $data['classroom_id'];
            $file = $req->file('file');
            $extension = $file->getClientOriginalExtension();

            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $imported = 0;
            $errors = [];
            $classroom = Classroom::find($classroomId);

            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue;
                }

                $nis = trim($row[0] ?? '');
                $name = trim($row[1] ?? '');
                $gender = strtoupper(trim($row[2] ?? ''));

                if (empty($nis) || empty($name)) {
                    continue;
                }
                if (! in_array($gender, ['L', 'P'])) {
                    $errors[] = 'Baris '.($index + 1).": Jenis kelamin harus L atau P (ditemukan: '$row[2]')";

                    continue;
                }
                if (Student::where('nis', $nis)->exists()) {
                    $errors[] = 'Baris '.($index + 1).": NIS $nis sudah terdaftar";

                    continue;
                }

                $user = User::create([
                    'name' => $name,
                    'email' => 'siswa'.$nis.'@madani.id',
                    'password' => bcrypt('siswa123'),
                    'role' => 'wali_murid',
                ]);

                Student::create([
                    'classroom_id' => $classroomId,
                    'user_id' => $user->id,
                    'nis' => $nis,
                    'name' => $name,
                    'gender' => $gender,
                ]);

                $imported++;
            }

            ActivityLogger::log('create', "Import {$imported} siswa ke {$classroom->name} via Excel");

            $message = "Berhasil mengimpor {$imported} siswa";
            if (! empty($errors)) {
                $message .= '. '.implode('<br>', $errors);
            }

            return redirect()->route('admin.students.index')->with('success', $message);
        })->name('admin.students.import');

        // Export Excel
        Route::get('/app/admin/students/export', function (Request $req) {
            $classroomId = $req->get('classroom_id');
            $query = Student::with('classroom')->orderBy('name');

            if ($classroomId) {
                $query->where('classroom_id', $classroomId);
                $classroom = Classroom::find($classroomId);
                $filename = 'siswa-'.str_replace(' ', '-', $classroom->name).'-'.date('Ymd').'.xlsx';
            } else {
                $filename = 'siswa-semua-'.date('Ymd').'.xlsx';
            }

            $students = $query->get();

            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'NIS');
            $sheet->setCellValue('B1', 'Nama');
            $sheet->setCellValue('C1', 'Jenis Kelamin');
            $sheet->setCellValue('D1', 'Kelas');
            $sheet->setCellValue('E1', 'Tingkat');
            $sheet->setCellValue('F1', 'Tahun Ajaran');
            $sheet->getStyle('A1:F1')->getFont()->setBold(true);

            $row = 2;
            foreach ($students as $s) {
                $sheet->setCellValue('A'.$row, $s->nis);
                $sheet->setCellValue('B'.$row, $s->name);
                $sheet->setCellValue('C'.$row, $s->gender);
                $sheet->setCellValue('D'.$row, $s->classroom?->name);
                $sheet->setCellValue('E'.$row, $s->classroom?->grade);
                $sheet->setCellValue('F'.$row, $s->classroom?->academic_year);
                $row++;
            }

            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        })->name('admin.students.export');

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
            ActivityLogger::log('create', 'Menambahkan mapping guru-mapel-kelas');

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

        Route::post('/app/admin/schedules', function (Request $req) {
            $data = $req->validate([
                'teacher_subject_id' => 'required|exists:teacher_subjects,id',
                'day' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
                'start_time' => 'required',
                'end_time' => 'required',
                'hour_order' => 'required|integer|min:1|max:4',
            ]);
            Schedule::create($data);
            ActivityLogger::log('create', 'Menambahkan jadwal: '.ucfirst($data['day']).' jam '.$data['start_time']);

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
            ActivityLogger::log('create', 'Menambahkan bobot nilai: '.$data['name'].' ('.$data['code'].')');

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

            $todayAttendances = TeacherAttendance::where('date', $today)->get();
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

        Route::post('/app/admin/users/{user}/reset-password', function (Request $req, User $user) {
            $data = $req->validate([
                'password' => 'required|min:6|confirmed',
            ]);

            $plainPassword = $data['password'];
            $user->update(['password' => bcrypt($plainPassword)]);

            ActivityLogger::log('update', 'Reset password: '.$user->name.' ('.$user->email.')');

            return redirect()->route('admin.users.index')->with('success', 'Password untuk <strong>'.$user->name.'</strong> berhasil direset.
            <div class="mt-sm p-sm bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <span class="font-mono text-blue-700 dark:text-blue-300 select-all text-[16px] tracking-wider">'.e($plainPassword).'</span>
                <button onclick="navigator.clipboard.writeText(\''.e($plainPassword).'\');this.textContent=\'Disalin!\'" class="ml-sm text-label-sm text-blue-600 hover:text-blue-800 underline" type="button">Salin</button>
            </div>');
        })->name('admin.users.reset-password');

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
