<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ScoreComponentController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\TeacherSubjectController;
use Illuminate\Support\Facades\Route;

// ===== Public Auth =====
Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:login');

// ===== Authenticated API =====
Route::middleware('auth:sanctum')->group(function () {

    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    // Read: Admin & Guru
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

    // Read: all roles
    Route::get('attendances', [AttendanceController::class, 'index']);
    Route::get('scores', [ScoreController::class, 'index']);
    Route::get('scores/rapor', [ScoreController::class, 'rapor']);
    Route::get('scores/rapor-pdf', [ScoreController::class, 'raporPdf']);

    // Write: Guru
    Route::middleware('role:guru')->group(function () {
        Route::get('teacher-attendances', [TeacherAttendanceController::class, 'index']);
        Route::get('teacher-attendances/today', [TeacherAttendanceController::class, 'today']);
        Route::get('teacher-attendances/history', [TeacherAttendanceController::class, 'history']);
        Route::post('teacher-attendances', [TeacherAttendanceController::class, 'store']);
        Route::post('teacher-attendances/check-in', [TeacherAttendanceController::class, 'checkIn']);
        Route::post('teacher-attendances/check-out', [TeacherAttendanceController::class, 'checkOut']);
        Route::get('teacher-attendances/{teacher_attendance}', [TeacherAttendanceController::class, 'show']);

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
        Route::post('scores/import', [ScoreController::class, 'importExcel'])->name('scores.import');
        Route::put('scores/{score}', [ScoreController::class, 'update']);
        Route::delete('scores/{score}', [ScoreController::class, 'destroy']);
        Route::get('scores/{score}', [ScoreController::class, 'show']);
    });

    // Admin Only
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
        Route::put('teacher-subjects/{teacher_subject}', [TeacherSubjectController::class, 'update']);
        Route::delete('teacher-subjects/{teacher_subject}', [TeacherSubjectController::class, 'destroy']);

        Route::post('schedules', [ScheduleController::class, 'store']);
        Route::put('schedules/{schedule}', [ScheduleController::class, 'update']);
        Route::delete('schedules/{schedule}', [ScheduleController::class, 'destroy']);

        Route::apiResource('score-components', ScoreComponentController::class);
    });
});
