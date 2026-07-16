<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\TeacherSubject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Schedule::with(['teacherSubject.user', 'teacherSubject.subject', 'teacherSubject.classroom']);

        if ($request->has('day')) {
            $query->where('day', $request->day);
        }

        if ($request->has('teacher_subject_id')) {
            $query->where('teacher_subject_id', $request->teacher_subject_id);
        }

        if ($request->has('classroom_id')) {
            $query->whereHas('teacherSubject', fn($q) => $q->where('classroom_id', $request->classroom_id));
        }

        $schedules = $query->orderBy('day')->orderBy('start_time')->get();

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'teacher_subject_id' => 'required|exists:teacher_subjects,id',
            'day' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'hour_order' => 'required|integer|min:1|max:4',
        ]);

        $startHour = (int) explode(':', $validated['start_time'])[0];
        $endHour = (int) explode(':', $validated['end_time'])[0];

        if ($startHour < 14 || $endHour > 16) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal harus berada dalam rentang blok sore (14:00 - 16:00 WIB)',
            ], 422);
        }

        $exists = Schedule::where('teacher_subject_id', $validated['teacher_subject_id'])
            ->where('day', $validated['day'])
            ->where('hour_order', $validated['hour_order'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Guru sudah memiliki jadwal di jam tersebut',
            ], 422);
        }

        $teacherSubject = TeacherSubject::find($validated['teacher_subject_id']);
        $conflict = Schedule::whereHas('teacherSubject', fn($q) => $q->where('classroom_id', $teacherSubject->classroom_id))
            ->where('day', $validated['day'])
            ->where('hour_order', $validated['hour_order'])
            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah ada jadwal lain di kelas yang sama pada jam tersebut',
            ], 422);
        }

        $schedule = Schedule::create($validated);
        $schedule->load(['teacherSubject.user', 'teacherSubject.subject', 'teacherSubject.classroom']);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dibuat',
            'data' => $schedule,
        ], 201);
    }

    public function show(Schedule $schedule): JsonResponse
    {
        $schedule->load(['teacherSubject.user', 'teacherSubject.subject', 'teacherSubject.classroom']);

        return response()->json([
            'success' => true,
            'data' => $schedule,
        ]);
    }

    public function update(Request $request, Schedule $schedule): JsonResponse
    {
        $validated = $request->validate([
            'day' => 'sometimes|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'hour_order' => 'sometimes|integer|min:1|max:4',
        ]);

        if (isset($validated['start_time']) || isset($validated['end_time'])) {
            $start = $validated['start_time'] ?? $schedule->start_time;
            $end = $validated['end_time'] ?? $schedule->end_time;
            $startHour = (int) explode(':', $start)[0];
            $endHour = (int) explode(':', $end)[0];

            if ($startHour < 14 || $endHour > 16) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal harus berada dalam rentang blok sore (14:00 - 16:00 WIB)',
                ], 422);
            }
        }

        $schedule->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil diperbarui',
            'data' => $schedule->fresh()->load(['teacherSubject.user', 'teacherSubject.subject', 'teacherSubject.classroom']),
        ]);
    }

    public function destroy(Schedule $schedule): JsonResponse
    {
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dihapus',
        ]);
    }

    public function getDaySchedule(Request $request): JsonResponse
    {
        $request->validate([
            'day' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        $schedules = Schedule::where('day', $request->day)
            ->whereHas('teacherSubject', fn($q) => $q->where('classroom_id', $request->classroom_id))
            ->with(['teacherSubject.user', 'teacherSubject.subject'])
            ->orderBy('hour_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }
}
