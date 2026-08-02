<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Score;
use App\Models\ScoreComponent;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seeder ini berisi akun & data DEMO — dilarang jalan di production.
        // Untuk hosting gunakan: php artisan db:seed --class=ProductionSeeder
        if (app()->environment('production')) {
            $this->command->error('DatabaseSeeder berisi data demo dan diblokir di production. Gunakan ProductionSeeder.');

            return;
        }

        // ===== User (idempotent) =====
        $admin = User::firstOrCreate(
            ['email' => 'admin@madani.id'],
            ['name' => 'Admin SMA', 'password' => Hash::make('admin123'), 'role' => 'admin', 'is_active' => true, 'must_change_password' => false]
        );

        $guru1 = User::firstOrCreate(
            ['email' => 'ahmad@madani.id'],
            ['name' => 'Ustaz Ahmad', 'password' => Hash::make('guru123'), 'role' => 'guru', 'is_active' => true, 'must_change_password' => false]
        );

        $guru2 = User::firstOrCreate(
            ['email' => 'fatimah@madani.id'],
            ['name' => 'Ustazah Fatimah', 'password' => Hash::make('guru123'), 'role' => 'guru', 'is_active' => true, 'must_change_password' => false]
        );

        // ===== Kelas =====
        $kelasX = Classroom::firstOrCreate(['name' => 'X IPA 1', 'grade' => 'X', 'academic_year' => '2025/2026']);
        $kelasXI = Classroom::firstOrCreate(['name' => 'XI IPA 1', 'grade' => 'XI', 'academic_year' => '2025/2026']);
        $kelasXII = Classroom::firstOrCreate(['name' => 'XII IPA 1', 'grade' => 'XII', 'academic_year' => '2025/2026']);

        // ===== Mata Pelajaran =====
        $mtk = Subject::firstOrCreate(['code' => 'MTK'], ['name' => 'Matematika']);
        $fis = Subject::firstOrCreate(['code' => 'FIS'], ['name' => 'Fisika']);
        $bing = Subject::firstOrCreate(['code' => 'BING'], ['name' => 'Bahasa Inggris']);
        $bind = Subject::firstOrCreate(['code' => 'BIND'], ['name' => 'Bahasa Indonesia']);

        // ===== Mapping Guru-Mapel-Kelas =====
        $ts1 = TeacherSubject::firstOrCreate(['user_id' => $guru1->id, 'subject_id' => $mtk->id, 'classroom_id' => $kelasX->id]);
        $ts2 = TeacherSubject::firstOrCreate(['user_id' => $guru1->id, 'subject_id' => $mtk->id, 'classroom_id' => $kelasXI->id]);
        $ts3 = TeacherSubject::firstOrCreate(['user_id' => $guru1->id, 'subject_id' => $fis->id, 'classroom_id' => $kelasX->id]);
        $ts4 = TeacherSubject::firstOrCreate(['user_id' => $guru2->id, 'subject_id' => $bing->id, 'classroom_id' => $kelasX->id]);
        $ts5 = TeacherSubject::firstOrCreate(['user_id' => $guru2->id, 'subject_id' => $bing->id, 'classroom_id' => $kelasXI->id]);
        $ts6 = TeacherSubject::firstOrCreate(['user_id' => $guru2->id, 'subject_id' => $bind->id, 'classroom_id' => $kelasXII->id]);

        // ===== Jadwal =====
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $schedules = [];
        $order = 0;
        foreach ([$kelasX, $kelasXI, $kelasXII] as $kelas) {
            foreach (array_slice($days, 0, 4) as $day) {
                $order++;
                $ts = TeacherSubject::where('classroom_id', $kelas->id)->first();
                if ($ts) {
                    $schedules[] = Schedule::create([
                        'teacher_subject_id' => $ts->id,
                        'day' => $day,
                        'start_time' => '14:00',
                        'end_time' => '14:50',
                        'hour_order' => 1,
                    ]);
                }
            }
        }

        // ===== Siswa (3 per kelas) =====
        $siswaData = [
            ['name' => 'Ali bin Abi', 'nis' => '1001', 'classroom_id' => $kelasX->id],
            ['name' => 'Bilal bin Rabbah', 'nis' => '1002', 'classroom_id' => $kelasX->id],
            ['name' => 'Hasan bin Ali', 'nis' => '1003', 'classroom_id' => $kelasX->id],
            ['name' => 'Aisyah binti Abu', 'nis' => '2001', 'classroom_id' => $kelasXI->id],
            ['name' => 'Fatimah binti Muhammad', 'nis' => '2002', 'classroom_id' => $kelasXI->id],
            ['name' => 'Khadijah binti Khuwailid', 'nis' => '2003', 'classroom_id' => $kelasXI->id],
            ['name' => 'Umar bin Khattab', 'nis' => '3001', 'classroom_id' => $kelasXII->id],
            ['name' => 'Utsman bin Affan', 'nis' => '3002', 'classroom_id' => $kelasXII->id],
            ['name' => 'Ali bin Abi Thalib', 'nis' => '3003', 'classroom_id' => $kelasXII->id],
        ];

        $students = [];
        $studentPasswords = [];
        foreach ($siswaData as $i => $data) {
            $password = Str::random(10);
            $user = User::firstOrCreate(
                ['email' => "siswa{$data['nis']}@madani.id"],
                ['name' => $data['name'], 'password' => Hash::make($password), 'role' => 'wali_murid', 'must_change_password' => true]
            );
            $studentPasswords[] = "{$data['name']} (NIS {$data['nis']}): {$password}";
            $students[] = Student::firstOrCreate(
                ['nis' => $data['nis']],
                ['user_id' => $user->id, 'classroom_id' => $data['classroom_id'], 'name' => $data['name'], 'gender' => $i < 3 ? 'L' : ($i < 6 ? 'P' : 'L'), 'is_active' => true]
            );
        }

        // ===== Bobot Komponen Nilai =====
        foreach ([$mtk, $fis, $bing, $bind] as $subject) {
            foreach (['ganjil', 'genap'] as $semester) {
                ScoreComponent::create(['subject_id' => $subject->id, 'code' => 'tugas', 'name' => 'Tugas', 'weight' => 20, 'semester' => $semester, 'academic_year' => '2025/2026']);
                ScoreComponent::create(['subject_id' => $subject->id, 'code' => 'ph', 'name' => 'Penilaian Harian', 'weight' => 30, 'semester' => $semester, 'academic_year' => '2025/2026']);
                ScoreComponent::create(['subject_id' => $subject->id, 'code' => 'uts', 'name' => 'UTS', 'weight' => 25, 'semester' => $semester, 'academic_year' => '2025/2026']);
                ScoreComponent::create(['subject_id' => $subject->id, 'code' => 'uas', 'name' => 'UAS', 'weight' => 25, 'semester' => $semester, 'academic_year' => '2025/2026']);
            }
        }

        // ===== Nilai Sample =====
        foreach ($students as $student) {
            $subjects = Subject::whereHas('teacherSubjects', fn ($q) => $q->where('classroom_id', $student->classroom_id))->get();
            foreach ($subjects as $subject) {
                Score::create(['student_id' => $student->id, 'subject_id' => $subject->id, 'component_code' => 'tugas', 'value' => rand(70, 100), 'description' => 'Tugas 1', 'teacher_id' => $guru1->id, 'semester' => 'ganjil', 'academic_year' => '2025/2026']);
                Score::create(['student_id' => $student->id, 'subject_id' => $subject->id, 'component_code' => 'ph', 'value' => rand(65, 95), 'description' => 'PH 1', 'teacher_id' => $guru1->id, 'semester' => 'ganjil', 'academic_year' => '2025/2026']);
                Score::create(['student_id' => $student->id, 'subject_id' => $subject->id, 'component_code' => 'uts', 'value' => rand(60, 100), 'description' => 'UTS Ganjil', 'teacher_id' => $guru1->id, 'semester' => 'ganjil', 'academic_year' => '2025/2026']);
                Score::create(['student_id' => $student->id, 'subject_id' => $subject->id, 'component_code' => 'uas', 'value' => rand(65, 100), 'description' => 'UAS Ganjil', 'teacher_id' => $guru1->id, 'semester' => 'ganjil', 'academic_year' => '2025/2026']);
            }
        }

        // ===== Absensi Sample =====
        foreach ($students as $student) {
            foreach ($schedules as $schedule) {
                if (rand(0, 3) > 0) {
                    Attendance::create([
                        'student_id' => $student->id,
                        'schedule_id' => $schedule->id,
                        'date' => '2025-08-'.str_pad(rand(1, 30), 2, '0', STR_PAD_LEFT),
                        'status' => ['H', 'H', 'H', 'H', 'S', 'I', 'A'][rand(0, 6)],
                        'submitted_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('Seeder berhasil: '.User::count().' user, '.Classroom::count().' kelas, '.Subject::count().' mapel, '.Student::count().' siswa');

        if (! empty($studentPasswords)) {
            $this->command->info('Password awal siswa (acak, wajib diganti saat login pertama):');
            foreach ($studentPasswords as $line) {
                $this->command->line($line);
            }
        }
    }
}
