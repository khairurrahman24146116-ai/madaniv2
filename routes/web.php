<?php

use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherSubjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => 'Madani-SMS',
        'version' => '1.0.0',
        'module' => 'Module 1 - Manajemen Kurikulum & Kelas',
    ]);
});

Route::apiResource('classrooms', ClassroomController::class);
Route::get('classrooms/{classroom}/students', [ClassroomController::class, 'getActiveStudents']);

Route::apiResource('students', StudentController::class);
Route::post('students/bulk-import', [StudentController::class, 'bulkImport']);

Route::apiResource('subjects', SubjectController::class);

Route::apiResource('teacher-subjects', TeacherSubjectController::class);
Route::get('teacher-subjects/schedule', [TeacherSubjectController::class, 'getTeacherSchedule']);

Route::apiResource('schedules', ScheduleController::class);
Route::get('schedules/day', [ScheduleController::class, 'getDaySchedule']);
