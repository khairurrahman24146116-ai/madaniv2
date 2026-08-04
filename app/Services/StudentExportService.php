<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Student;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Service Export Siswa ke file Excel.
 *
 * Memindahkan logika pembuatan spreadsheet dari closure route admin/students/export
 * ke tempat yang reusable dan tidak bergantung pada output langsung php://output.
 */
class StudentExportService
{
    /**
     * Bangun spreadsheet berisi data siswa (opsional difilter per kelas) dan
     * kembalikan sebagai file unduhan streaming.
     */
    public function export(?int $classroomId): StreamedResponse
    {
        $query = Student::with('classroom')->select(['id', 'nis', 'name', 'gender', 'classroom_id'])->orderBy('name');

        if ($classroomId) {
            $query->where('classroom_id', $classroomId);
            $classroom = Classroom::find($classroomId);
            $filename = 'siswa-'.str_replace(' ', '-', $classroom->name).'-'.date('Ymd').'.xlsx';
        } else {
            $filename = 'siswa-semua-'.date('Ymd').'.xlsx';
        }

        $students = $query->get();

        return response()->streamDownload(function () use ($students) {
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

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
