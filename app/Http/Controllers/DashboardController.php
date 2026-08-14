<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Web (guru): dashboard jadwal mengajar hari ini.
     */
    public function index(): View
    {
        $user = auth()->user();

        TeacherAttendanceController::autoAttendIfWithinSoreHours($user);

        $schedules = Schedule::with('teacherSubject.subject', 'teacherSubject.classroom', 'teacherSubject.user')
            ->where('day', now()->locale('id')->isoFormat('dddd'))
            ->when(! $user->isAdmin(), fn ($query) => $query->whereHas('teacherSubject', fn ($q) => $q->where('user_id', $user->id)))
            ->get()
            ->unique(fn ($schedule) => implode('|', [$schedule->day, $schedule->start_time, $schedule->teacher_subject_id]));

        $activeSchedule = $schedules->first();

        return view('dashboard', [
            'todaySessions' => $schedules->count(),
            'upcomingSchedules' => $schedules->take(3)->map(function ($s) {
                $now = now();
                $start = Carbon::createFromFormat('H:i:s', $s->start_time);
                $end = Carbon::createFromFormat('H:i:s', $s->end_time);

                $status = $now->lt($start)
                    ? 'menunggu'
                    : ($now->lte($end) ? 'berlangsung' : 'selesai');

                return [
                    'id' => $s->id,
                    'time' => $s->start_time.' - '.$s->end_time,
                    'subject' => $s->teacherSubject->subject->name ?? '',
                    'code' => $s->teacherSubject->subject->code ?? '',
                    'class' => $s->teacherSubject->classroom->name ?? '',
                    'status' => $status,
                ];
            })->values(),
            'activeClass' => $activeSchedule
                ? ($activeSchedule->teacherSubject->subject->name ?? '').' - '.($activeSchedule->teacherSubject->classroom->name ?? '')
                : null,
            'activeRoom' => $activeSchedule ? ($activeSchedule->teacherSubject->classroom->name ?? '') : null,
            'studentCount' => $activeSchedule?->teacherSubject->classroom->students()->count(),
            'pendingGrades' => null,
            'attendanceRate' => null,
        ]);
    }
}
