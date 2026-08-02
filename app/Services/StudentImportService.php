<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Service Import Siswa via file Excel (xlsx/xls/csv).
 *
 * Memindahkan logika parsing/validasi/insert dari closure route admin/students/import
 * supaya route hanya memanggil controller yang memanggil service ini.
 */
class StudentImportService
{
    /**
     * Baca file Excel, validasi tiap baris, lalu insert siswa (beserta akun wali murid).
     *
     * Baris pertama dianggap header dan dilewati. Baris tanpa NIS atau nama dilewati
     * diam-diam. Baris dengan gender bukan L/P atau NIS duplikat dicatat sebagai error
     * tanpa menghentikan proses.
     *
     * @return array{imported: int, errors: array<int, string>}
     */
    public function import(UploadedFile $file, int $classroomId): array
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $imported = 0;
        $errors = [];

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

            try {
                DB::transaction(function () use ($classroomId, $nis, $name, $gender, &$imported) {
                    $user = User::create([
                        'name' => $name,
                        'email' => 'siswa'.$nis.'@madani.id',
                        'password' => Str::random(10),
                        'role' => 'wali_murid',
                        'must_change_password' => true,
                    ]);

                    Student::create([
                        'classroom_id' => $classroomId,
                        'user_id' => $user->id,
                        'nis' => $nis,
                        'name' => $name,
                        'gender' => $gender,
                    ]);

                    $imported++;
                });
            } catch (\Throwable $e) {
                $errors[] = 'Baris '.($index + 1).": gagal disimpan ($e->getMessage())";
            }
        }

        return ['imported' => $imported, 'errors' => $errors];
    }
}
