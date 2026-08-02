<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Services\ActivityLogger;
use App\Services\StudentExportService;
use App\Services\StudentImportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller web untuk import & export data siswa via Excel.
 *
 * Route hanya mendelegasikan ke sini, service yang menangani logika
 * parsing/validasi/insert (StudentImportService) dan pembuatan file
 * (StudentExportService). Tidak ada logic Excel di closure route.
 */
class StudentImportExportController extends Controller
{
    public function importForm()
    {
        $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();

        return view('admin.students.import', compact('classrooms'));
    }

    public function import(Request $request)
    {
        $data = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        $result = app(StudentImportService::class)->import(
            $data['file'],
            (int) $data['classroom_id']
        );

        $classroom = Classroom::find($data['classroom_id']);
        ActivityLogger::log('create', "Import {$result['imported']} siswa ke {$classroom->name} via Excel");

        $message = "Berhasil mengimpor {$result['imported']} siswa";
        if (! empty($result['errors'])) {
            $message .= '. '.implode('<br>', $result['errors']);
        }

        return redirect()->route('admin.students.index')->with('success', $message);
    }

    public function export(Request $request): StreamedResponse
    {
        return app(StudentExportService::class)->export(
            $request->integer('classroom_id')
        );
    }
}
