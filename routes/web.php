<?php

use App\Http\Controllers\ActiveLetterController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BendaharaController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ScoreComponentController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\SPPController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentImportExportController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\TeacherSubjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaliMuridController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'redirectHome']);

// ===== Web Login =====
Route::post('/auth/login/web', [AuthController::class, 'loginWeb'])->name('auth.login.web')->middleware('throttle:5,1');
Route::post('/auth/logout/web', [AuthController::class, 'logoutWeb'])->name('auth.logout.web');

// ===== Ganti Password Wajib (must_change_password) =====
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/app/ganti-password', [PasswordChangeController::class, 'showForm'])->name('password.change');
    Route::post('/app/ganti-password', [PasswordChangeController::class, 'update'])->name('password.change.update');
});

Route::get('/login', [HomeController::class, 'login'])->name('login');

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
    Route::get('/app/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/app/profile', [ProfileController::class, 'update'])->name('profile.update');

    // ===== Wali Murid: Dashboard & Rapor =====
    Route::middleware('role:wali_murid')->group(function () {
        Route::get('/app/wali-murid', [WaliMuridController::class, 'dashboard'])->name('wali-murid.dashboard');

        Route::get('/app/wali-murid/surat', [LetterController::class, 'waliIndex'])->name('wali.letters.index');
        Route::get('/app/wali-murid/surat/{letter}', [LetterController::class, 'show'])->name('wali.letters.show');

        Route::get('/app/wali-murid/kontak', [ContactController::class, 'waliIndex'])->name('wali.contact.index');
        Route::get('/app/wali-murid/kontak/buat', [ContactController::class, 'waliCreate'])->name('wali.contact.create');
        Route::post('/app/wali-murid/kontak', [ContactController::class, 'waliStore'])->name('wali.contact.store');

        Route::get('/app/wali-murid/pertemuan', [MeetingController::class, 'waliIndex'])->name('wali.meetings.index');
        Route::get('/app/wali-murid/pertemuan/buat', [MeetingController::class, 'waliCreate'])->name('wali.meetings.create');
        Route::post('/app/wali-murid/pertemuan', [MeetingController::class, 'waliStore'])->name('wali.meetings.store');

        Route::get('/app/wali-murid/rapor/{student}', [WaliMuridController::class, 'rapor'])->name('wali-murid.rapor');
    });

    // ===== Guru: dashboard dan operasional mengajar =====
    Route::middleware('role:guru')->group(function () {

        Route::get('/app/guru/surat', [LetterController::class, 'guruIndex'])->name('guru.letters.index');
        Route::get('/app/guru/surat/{letter}', [LetterController::class, 'show'])->name('guru.letters.show');

        Route::get('/app/teacher-attendances/form', [TeacherAttendanceController::class, 'webForm'])->name('teacher.attendances.form');

        Route::post('/app/teacher-attendances/check-in', [TeacherAttendanceController::class, 'checkIn'])->name('teacher.attendances.checkin');
        Route::post('/app/teacher-attendances/check-out', [TeacherAttendanceController::class, 'checkOut'])->name('teacher.attendances.checkout');
        Route::post('/app/teacher-attendances', [TeacherAttendanceController::class, 'store'])->name('teacher.attendances.store');

        // Simpan nilai dari halaman web (session-aware, bukan API bearer).
        Route::post('/app/scores/batch', [ScoreController::class, 'batchStore'])->name('scores.batch.web');

        Route::get('/app/teacher-attendances', [TeacherAttendanceController::class, 'webIndex'])->name('teacher.attendances.index');

        Route::get('/app/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/app/attendances', [AttendanceController::class, 'webIndex'])->name('attendances.index');

        Route::get('/app/schedules', [ScheduleController::class, 'webIndex'])->name('schedules.index');

        Route::get('/app/schedules/mobile', [ScheduleController::class, 'webMobile'])->name('schedules.mobile');

        Route::get('/app/scores/rapor-preview', [ScoreController::class, 'webRaporPreview'])->name('scores.rapor-preview');

        Route::get('/app/attendances/form', [AttendanceController::class, 'webForm'])->name('attendances.form');

        Route::post('/app/attendances', [AttendanceController::class, 'store'])->name('attendances.store');

        Route::get('/app/attendances/realtime', [AttendanceController::class, 'webRealtime'])->name('attendances.realtime');

        Route::get('/app/scores/input', [ScoreController::class, 'webCreate'])->name('scores.create');
    });
});

// ===== Web Admin CRUD Views (auth + admin only) =====
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:admin')->group(function () {

        Route::get('/app/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');

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
        Route::get('/app/admin/students', [StudentController::class, 'webIndex'])->name('admin.students.index');
        Route::get('/app/admin/students/create', [StudentController::class, 'webCreate'])->name('admin.students.create');
        Route::post('/app/admin/students', [StudentController::class, 'webStore'])->name('admin.students.store');
        Route::get('/app/admin/students/{student}/edit', [StudentController::class, 'webEdit'])->name('admin.students.edit');
        Route::put('/app/admin/students/{student}', [StudentController::class, 'webUpdate'])->name('admin.students.update');
        Route::delete('/app/admin/students/{student}', [StudentController::class, 'webDestroy'])->name('admin.students.destroy');

        // Pindah kelas
        Route::get('/app/admin/students/{student}/move', [StudentController::class, 'webMoveForm'])->name('admin.students.move-form');
        Route::post('/app/admin/students/{student}/move', [StudentController::class, 'webMove'])->name('admin.students.move');

        // Import & Export Excel
        Route::get('/app/admin/students/import', [StudentImportExportController::class, 'importForm'])->name('admin.students.import-form');
        Route::post('/app/admin/students/import', [StudentImportExportController::class, 'import'])->name('admin.students.import');
        Route::get('/app/admin/students/export', [StudentImportExportController::class, 'export'])->name('admin.students.export');

        // Teacher-Subjects
        Route::get('/app/admin/teacher-subjects', [TeacherSubjectController::class, 'webIndex'])->name('admin.teacher-subjects.index');
        Route::get('/app/admin/teacher-subjects/create', [TeacherSubjectController::class, 'webCreate'])->name('admin.teacher-subjects.create');
        Route::post('/app/admin/teacher-subjects', [TeacherSubjectController::class, 'webStore'])->name('admin.teacher-subjects.store');
        Route::get('/app/admin/teacher-subjects/{teacher_subject}/edit', [TeacherSubjectController::class, 'webEdit'])->name('admin.teacher-subjects.edit');
        Route::put('/app/admin/teacher-subjects/{teacher_subject}', [TeacherSubjectController::class, 'webUpdate'])->name('admin.teacher-subjects.update');
        Route::delete('/app/admin/teacher-subjects/{teacher_subject}', [TeacherSubjectController::class, 'webDestroy'])->name('admin.teacher-subjects.destroy');

        // ===== Schedules =====
        Route::get('/app/admin/schedules', [ScheduleController::class, 'webAdminIndex'])->name('admin.schedules.index');
        Route::get('/app/admin/schedules/create', [ScheduleController::class, 'webAdminCreate'])->name('admin.schedules.create');
        Route::post('/app/admin/schedules', [ScheduleController::class, 'webAdminStore'])->name('admin.schedules.store');
        Route::get('/app/admin/schedules/{schedule}/edit', [ScheduleController::class, 'webAdminEdit'])->name('admin.schedules.edit');
        Route::put('/app/admin/schedules/{schedule}', [ScheduleController::class, 'webAdminUpdate'])->name('admin.schedules.update');
        Route::delete('/app/admin/schedules/{schedule}', [ScheduleController::class, 'webAdminDestroy'])->name('admin.schedules.destroy');

        // ===== Score Components =====
        Route::get('/app/admin/score-components', [ScoreComponentController::class, 'webIndex'])->name('admin.score-components.index');
        Route::get('/app/admin/score-components/create', [ScoreComponentController::class, 'webCreate'])->name('admin.score-components.create');
        Route::post('/app/admin/score-components', [ScoreComponentController::class, 'webStore'])->name('admin.score-components.store');
        Route::get('/app/admin/score-components/{score_component}/edit', [ScoreComponentController::class, 'webEdit'])->name('admin.score-components.edit');
        Route::put('/app/admin/score-components/{score_component}', [ScoreComponentController::class, 'webUpdate'])->name('admin.score-components.update');
        Route::delete('/app/admin/score-components/{score_component}', [ScoreComponentController::class, 'webDestroy'])->name('admin.score-components.destroy');

        // ===== Teacher Attendance (Admin) =====
        Route::get('/app/admin/teacher-attendances', [TeacherAttendanceController::class, 'webAdminIndex'])->name('admin.teacher-attendances.index');

        // ===== Users Management =====
        Route::get('/app/admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/app/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/app/admin/users', [UserController::class, 'store'])->name('admin.users.store')->middleware('throttle:10,1');
        Route::get('/app/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/app/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update')->middleware('throttle:10,1');
        Route::delete('/app/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy')->middleware('throttle:10,1');
        Route::post('/app/admin/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password')->middleware('throttle:10,1');
        Route::post('/app/admin/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('admin.users.toggle-active');

        // Halaman khusus yang menampilkan password SEKALI setelah reset.
        // Password diambil dari session single-use lalu dihapus, sehingga
        // refresh/back/tebus tidak akan menampilkan lagi.
        Route::get('/app/admin/users/{user}/password-reveal', [UserController::class, 'passwordReveal'])->name('admin.users.password-reveal')->middleware('throttle:10,1');

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
        Route::get('/app/admin/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity-logs.index');
    });

    // ===== PDF Rapor (semua role) =====
    Route::get('/app/rapor-pdf', [ScoreController::class, 'raporPdf'])->name('rapor.pdf');

    // ===== Cetak Surat (semua role) =====
    Route::get('/app/surat/cetak/{letter}', [LetterController::class, 'printPdf'])->name('letters.print');

    // ===== SPP (semua role) =====
    Route::get('/app/spp', [SPPController::class, 'index'])->name('spp.index');
    Route::get('/app/spp/bayar', [SPPController::class, 'payer'])->name('spp.payer');

    // ===== Bendahara (keuangan) =====
    Route::middleware('role:bendahara')->group(function () {
        Route::get('/app/bendahara', [BendaharaController::class, 'dashboard'])->name('bendahara.dashboard');
        Route::get('/app/bendahara/rekap', [BendaharaController::class, 'rekap'])->name('bendahara.rekap');
        Route::get('/app/bendahara/rekap/export', [BendaharaController::class, 'exportCsv'])->name('bendahara.rekap.export');

        // Pencatatan SPP (bayar/batal) — HANYA bendahara. Admin hanya melihat.
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
