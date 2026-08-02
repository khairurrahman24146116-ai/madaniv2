<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\TeacherAttendance;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherAttendanceController extends Controller
{
    private function getTeacherScheduleIds(Request $request): ?array
    {
        $user = $request->user();
        if ($user->isAdmin()) {
            return null;
        }

        return $user->teacherSubjects()->with('schedules')->get()
            ->flatMap(fn ($ts) => $ts->schedules->pluck('id'))
            ->unique()
            ->values()
            ->toArray();
    }

    public function index(Request $request): JsonResponse
    {
        $query = TeacherAttendance::with([
            'user',
            'schedule.teacherSubject.subject',
            'schedule.teacherSubject.classroom',
        ]);

        $user = $request->user();
        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('user_id') && $user->isAdmin()) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $attendances = $query->orderBy('date', 'desc')->orderBy('check_in', 'desc')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $attendances,
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'schedule_id' => 'nullable|exists:schedules,id',
            'date' => 'required|date_format:Y-m-d',
            'status' => 'required|in:H,S,I,A',
            'notes' => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        $schedule = null;
        if (! empty($validated['schedule_id'])) {
            $schedule = Schedule::with('teacherSubject')->findOrFail($validated['schedule_id']);

            if (! $user->isAdmin()) {
                $ownedIds = $this->getTeacherScheduleIds($request);
                if (! in_array($schedule->id, $ownedIds)) {
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke jadwal ini'], 403);
                    }

                    return redirect()->route('teacher.attendances.form')->with('error', 'Anda tidak memiliki akses ke jadwal ini');
                }
            }
        }

        if (! $user->isAdmin() && ! app()->environment('testing')) {
            $now = now()->setTimezone('Asia/Jakarta');
            $currentHour = (int) $now->format('H');
            if ($currentHour < 14 || $currentHour >= 16) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Absensi guru hanya dapat diisi dalam rentang blok sore (14:00 - 16:00 WIB)'], 422);
                }

                return redirect()->route('teacher.attendances.form')->with('error', 'Absensi guru hanya dapat diisi dalam rentang blok sore (14:00 - 16:00 WIB)');
            }
        }

        $result = DB::transaction(function () use ($user, $validated, $schedule) {
            $data = [
                'user_id' => $user->id,
                'schedule_id' => $schedule?->id,
                'date' => $validated['date'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ];

            if ($validated['status'] === 'H' && ! $request->has('check_out')) {
                $data['check_in'] = now();
            }

            return TeacherAttendance::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => $validated['date'],
                ],
                $data
            );
        });

        ActivityLogger::log('create', 'Absensi guru: '.$user->name.' ('.$validated['date'].')');

        if ($request->expectsJson()) {
            $result->load(['user', 'schedule.teacherSubject.subject']);

            return response()->json([
                'success' => true,
                'message' => 'Absensi guru berhasil disimpan',
                'data' => $result,
            ], 201);
        }

        return redirect()->route('teacher.attendances.form')
            ->with('success', 'Absensi guru berhasil disimpan');
    }

    public function checkIn(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'schedule_id' => 'nullable|exists:schedules,id',
            'date' => 'required|date_format:Y-m-d',
            'notes' => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        $schedule = null;
        if (! empty($validated['schedule_id'])) {
            $schedule = Schedule::with('teacherSubject')->findOrFail($validated['schedule_id']);
        }

        $result = DB::transaction(function () use ($user, $validated, $schedule) {
            return TeacherAttendance::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => $validated['date'],
                ],
                [
                    'schedule_id' => $schedule?->id,
                    'status' => 'H',
                    'check_in' => now(),
                    'notes' => $validated['notes'] ?? null,
                ]
            );
        });

        ActivityLogger::log('create', 'Check-in guru: '.$user->name.' ('.$validated['date'].')');

        if ($request->expectsJson()) {
            $result->load(['user', 'schedule.teacherSubject.subject']);

            return response()->json([
                'success' => true,
                'message' => 'Check-in berhasil',
                'data' => $result,
            ]);
        }

        return redirect()->route('teacher.attendances.form')
            ->with('success', 'Check-in berhasil');
    }

    public function checkOut(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'notes' => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        $attendance = TeacherAttendance::where('user_id', $user->id)
            ->where('date', $validated['date'])
            ->first();

        if (! $attendance) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Belum ada absensi untuk hari ini'], 404);
            }

            return redirect()->route('teacher.attendances.form')->with('error', 'Belum ada absensi untuk hari ini');
        }

        $attendance->update([
            'check_out' => now(),
            'notes' => $validated['notes'] ?? $attendance->notes,
        ]);

        ActivityLogger::log('update', 'Check-out guru: '.$user->name.' ('.$validated['date'].')');

        if ($request->expectsJson()) {
            $attendance->load(['user', 'schedule.teacherSubject.subject']);

            return response()->json([
                'success' => true,
                'message' => 'Check-out berhasil',
                'data' => $attendance,
            ]);
        }

        return redirect()->route('teacher.attendances.form')
            ->with('success', 'Check-out berhasil');
    }

    public function show(TeacherAttendance $teacherAttendance): JsonResponse
    {
        $this->authorizeAttendance($teacherAttendance);

        $teacherAttendance->load(['user', 'schedule.teacherSubject.subject', 'schedule.teacherSubject.classroom']);

        return response()->json([
            'success' => true,
            'data' => $teacherAttendance,
        ]);
    }

    public function today(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = now()->format('Y-m-d');

        $attendance = TeacherAttendance::where('user_id', $user->id)
            ->where('date', $today)
            ->with(['schedule.teacherSubject.subject', 'schedule.teacherSubject.classroom'])
            ->first();

        return response()->json([
            'success' => true,
            'data' => $attendance,
            'is_checked_in' => $attendance !== null && $attendance->check_in !== null,
            'is_checked_out' => $attendance !== null && $attendance->check_out !== null,
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = TeacherAttendance::where('user_id', $user->id)
            ->with(['schedule.teacherSubject.subject', 'schedule.teacherSubject.classroom']);

        if ($request->has('month')) {
            $query->whereMonth('date', $request->month);
        }

        if ($request->has('year')) {
            $query->whereYear('date', $request->year);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $attendances,
        ]);
    }

    public static function autoAttend(int $userId, ?int $scheduleId = null, ?string $date = null): TeacherAttendance
    {
        $date = $date ?? now()->format('Y-m-d');

        return TeacherAttendance::updateOrCreate(
            [
                'user_id' => $userId,
                'date' => $date,
            ],
            [
                'schedule_id' => $scheduleId,
                'status' => 'H',
                'check_in' => now(),
            ]
        );
    }

    /**
     * Auto-attend guru bila sedang dalam jam sore dan belum ada catatan
     * check-in hari ini. Admin dilewati (tidak menerapkan auto-attendance).
     *
     * Dipanggil dari halaman dashboard & form absensi guru supaya logic
     * auto-attend hanya terdefinisi di satu tempat.
     */
    public static function autoAttendIfWithinSoreHours(User $user): void
    {
        if ($user->isAdmin() || ! AttendanceController::isWithinSoreHours()) {
            return;
        }

        $existing = TeacherAttendance::where('user_id', $user->id)
            ->where('date', now()->format('Y-m-d'))
            ->first();

        if (! $existing || ! $existing->check_in) {
            self::autoAttend($user->id);
        }
    }

    private function authorizeAttendance(TeacherAttendance $attendance): void
    {
        $user = request()->user();
        if ($user->isAdmin()) {
            return;
        }

        if ($attendance->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke data absensi ini');
        }
    }
}
