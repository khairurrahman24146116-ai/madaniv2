<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\Student;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller Attendance — Modul Absensi Digital Sore (Fase 2).
 *
 * FR-2.1: Guru melakukan absensi per jam pelajaran.
 * FR-2.2: Status H/S/I/A (Hadir, Sakit, Izin, Alpa).
 * FR-2.3: Pembatasan akses di luar jam 14:00-16:00 WIB.
 * FR-2.4: Timestamp otomatis setiap submit.
 */
class AttendanceController extends Controller
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

    /**
     * Menampilkan riwayat absensi.
     * Filter: date, schedule_id, classroom_id, student_id, status.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Attendance::with([
            'student.user',
            'schedule.teacherSubject.subject',
            'schedule.teacherSubject.classroom',
        ]);

        $scheduleIds = $this->getTeacherScheduleIds($request);
        if ($scheduleIds !== null) {
            $query->whereIn('schedule_id', $scheduleIds);
        }

        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        if ($request->has('schedule_id')) {
            $query->where('schedule_id', $request->schedule_id);
        }

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('classroom_id')) {
            $query->whereHas('schedule.teacherSubject', fn ($q) => $q->where('classroom_id', $request->classroom_id));
        }

        $attendances = $query->orderBy('date', 'desc')->orderBy('submitted_at', 'desc')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $attendances,
        ]);
    }

    /**
     * Submit absensi secara batch untuk satu jadwal.
     *
     * FR-2.1: Guru submit absensi untuk jam pelajaran yang sedang berjalan.
     * FR-2.3: Validasi waktu hanya dalam blok sore 14:00-16:00 WIB.
     * FR-2.4: Mencatat submitted_at otomatis.
     *
     * Body: { schedule_id, date, attendances: [{ student_id, status, notes? }] }
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'date' => 'required|date_format:Y-m-d',
            'attendances' => 'required|array|min:1',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:H,S,I,A',
            'attendances.*.notes' => 'nullable|string|max:255',
        ]);

        $schedule = Schedule::with('teacherSubject.classroom')->findOrFail($validated['schedule_id']);
        $classroomId = $schedule->teacherSubject->classroom_id;

        // RBAC: Guru hanya bisa absen untuk jadwal miliknya
        $user = $request->user();
        if (! $user->isAdmin()) {
            $ownedIds = $this->getTeacherScheduleIds($request);
            if (! in_array($schedule->id, $ownedIds)) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke jadwal ini'], 403);
                }

                return redirect()->route('attendances.form')->with('error', 'Anda tidak memiliki akses ke jadwal ini');
            }
        }

        // FR-2.3: Validasi waktu blok sore (14:00 - 16:00 WIB)
        // Admin dapat mengisi absensi kapan saja
        if (! $user->isAdmin() && ! app()->environment('testing')) {
            $now = now()->setTimezone('Asia/Jakarta');
            $currentHour = (int) $now->format('H');
            if ($currentHour < 14 || $currentHour >= 16) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Absensi hanya dapat diisi dalam rentang blok sore (14:00 - 16:00 WIB)'], 422);
                }

                return redirect()->route('attendances.form')->with('error', 'Absensi hanya dapat diisi dalam rentang blok sore (14:00 - 16:00 WIB)');
            }
        }

        // Validasi: semua student_id harus dari kelas yang sesuai dengan jadwal
        $studentIds = collect($validated['attendances'])->pluck('student_id')->unique();
        $validStudentIds = Student::where('classroom_id', $classroomId)
            ->whereIn('id', $studentIds)
            ->pluck('id');

        $invalidIds = $studentIds->diff($validStudentIds);
        if ($invalidIds->isNotEmpty()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Siswa dengan ID '.$invalidIds->join(', ').' tidak terdaftar di kelas jadwal ini'], 422);
            }

            return redirect()->route('attendances.form')->with('error', 'Siswa dengan ID '.$invalidIds->join(', ').' tidak terdaftar di kelas jadwal ini');
        }

        // FR-2.4: Catat timestamp submit
        $submittedAt = now();

        $results = DB::transaction(function () use ($validated, $submittedAt) {
            $created = [];
            foreach ($validated['attendances'] as $item) {
                $attendance = Attendance::updateOrCreate(
                    [
                        'student_id' => $item['student_id'],
                        'schedule_id' => $validated['schedule_id'],
                        'date' => $validated['date'],
                    ],
                    [
                        'status' => $item['status'],
                        'submitted_at' => $submittedAt,
                        'notes' => $item['notes'] ?? null,
                    ]
                );
                $created[] = $attendance;
            }

            return $created;
        });

        // Auto-attend guru yang mengisi absensi
        TeacherAttendanceController::autoAttend($user->id, $schedule->id, $validated['date']);

        $studentCount = count($validated['attendances']);
        ActivityLogger::log('create', 'Mengisi absensi: '.$studentCount.' siswa (jadwal '.$schedule->id.')');

        if ($request->expectsJson()) {
            $results = Attendance::with(['student.user', 'schedule.teacherSubject.subject'])
                ->whereIn('id', collect($results)->pluck('id'))
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil disimpan',
                'data' => $results,
            ], 201);
        }

        return redirect()->route('attendances.form', ['schedule_id' => $validated['schedule_id'], 'date' => $validated['date']])
            ->with('success', 'Absensi berhasil disimpan');
    }

    /**
     * Menampilkan detail satu record absensi.
     */
    public function show(Attendance $attendance): JsonResponse
    {
        $this->authorizeAttendance($attendance);

        $attendance->load(['student.user', 'schedule.teacherSubject.subject', 'schedule.teacherSubject.classroom']);

        return response()->json([
            'success' => true,
            'data' => $attendance,
        ]);
    }

    public function update(Request $request, Attendance $attendance): JsonResponse
    {
        $this->authorizeAttendance($attendance);

        $validated = $request->validate([
            'status' => 'sometimes|in:H,S,I,A',
            'notes' => 'nullable|string|max:255',
        ]);

        if (isset($validated['status'])) {
            $validated['submitted_at'] = now();
        }

        $attendance->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil diperbarui',
            'data' => $attendance->fresh()->load(['student.user', 'schedule.teacherSubject.subject']),
        ]);
    }

    public function destroy(Attendance $attendance): JsonResponse
    {
        $this->authorizeAttendance($attendance);

        $attendance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dihapus',
        ]);
    }

    public function form(Request $request): JsonResponse
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'date' => 'required|date_format:Y-m-d',
        ]);

        $schedule = Schedule::with([
            'teacherSubject.user',
            'teacherSubject.subject',
            'teacherSubject.classroom',
        ])->findOrFail($request->schedule_id);

        $user = $request->user();
        if (! $user->isAdmin()) {
            $ownedIds = $this->getTeacherScheduleIds($request);
            if (! in_array($schedule->id, $ownedIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke jadwal ini',
                ], 403);
            }
        }

        $classroomId = $schedule->teacherSubject->classroom_id;
        $students = Student::where('classroom_id', $classroomId)
            ->where('is_active', true)
            ->with('user')
            ->orderBy('name')
            ->get();

        $existingAttendances = Attendance::where('schedule_id', $request->schedule_id)
            ->where('date', $request->date)
            ->get()
            ->keyBy('student_id');

        $data = $students->map(function ($student) use ($existingAttendances) {
            $attendance = $existingAttendances->get($student->id);

            return [
                'student_id' => $student->id,
                'nis' => $student->nis,
                'student_name' => $student->name,
                'status' => $attendance?->status,
                'notes' => $attendance?->notes,
                'submitted_at' => $attendance?->submitted_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'schedule' => [
                    'id' => $schedule->id,
                    'teacher' => $schedule->teacherSubject->user->only(['id', 'name']),
                    'subject' => $schedule->teacherSubject->subject->only(['id', 'name', 'code']),
                    'classroom' => $schedule->teacherSubject->classroom->only(['id', 'name', 'grade']),
                    'day' => $schedule->day,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'hour_order' => $schedule->hour_order,
                ],
                'date' => $request->date,
                'students' => $data,
                'can_edit' => $user->isAdmin() || self::isWithinSoreHours(),
            ],
        ]);
    }

    /**
     * Ekspor data absensi ke CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Attendance::with([
            'student.user',
            'schedule.teacherSubject.subject',
            'schedule.teacherSubject.classroom',
        ]);

        $scheduleIds = $this->getTeacherScheduleIds($request);
        if ($scheduleIds !== null) {
            $query->whereIn('schedule_id', $scheduleIds);
        }

        if ($request->has('date')) {
            $query->where('date', $request->date);
        }
        if ($request->has('schedule_id')) {
            $query->where('schedule_id', $request->schedule_id);
        }
        if ($request->has('classroom_id')) {
            $query->whereHas('schedule.teacherSubject', fn ($q) => $q->where('classroom_id', $request->classroom_id));
        }

        $attendances = $query->orderBy('date', 'desc')->orderBy('submitted_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="absensi.csv"',
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Mapel', 'Status', 'Keterangan', 'Jam', 'Diinput']);

            foreach ($attendances as $a) {
                fputcsv($file, [
                    $a->date,
                    $a->student->nis,
                    $a->student->name,
                    $a->schedule->teacherSubject->classroom->name ?? '',
                    $a->schedule->teacherSubject->subject->name ?? '',
                    $a->status,
                    $a->notes ?? '',
                    "{$a->schedule->start_time}-{$a->schedule->end_time}",
                    $a->submitted_at ? date('Y-m-d H:i', strtotime($a->submitted_at)) : '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function authorizeAttendance(Attendance $attendance): void
    {
        $user = request()->user();
        if ($user->isAdmin()) {
            return;
        }
        $ownedIds = $this->getTeacherScheduleIds(request());
        if (! in_array($attendance->schedule_id, $ownedIds)) {
            abort(403, 'Anda tidak memiliki akses ke data absensi ini');
        }
    }

    /**
     * Cek apakah waktu saat ini dalam blok sore (14:00-16:00 WIB).
     * FR-2.3: Pembatasan akses pengisian absensi.
     */
    public static function isWithinSoreHours(): bool
    {
        $hour = (int) now()->setTimezone('Asia/Jakarta')->format('H');

        return $hour >= 14 && $hour < 16;
    }
}
