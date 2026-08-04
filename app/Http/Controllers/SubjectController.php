<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller Subject — CRUD mata pelajaran.
 *
 * Fase 1 (FR-1.1): Menyediakan daftar mata pelajaran SMA yang bisa
 * dipetakan ke guru dan kelas melalui tabel teacher_subjects.
 */
class SubjectController extends Controller
{
    /**
     * Menampilkan daftar mata pelajaran dengan jumlah guru pengajar.
     * Filter: search (nama/kode mapel).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Subject::withCount('teachers');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "{$search}%");
            });
        }

        $subjects = $query->orderBy('name')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $subjects,
        ]);
    }

    /**
     * Menambah mata pelajaran baru.
     * Kode mapel bersifat unik (misal: MTK, BIN, BING).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:subjects,code',
            'description' => 'nullable|string',
        ]);

        $subject = Subject::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Mata pelajaran berhasil dibuat',
            'data' => $subject,
        ], 201);
    }

    /**
     * Menampilkan detail mata pelajaran dan guru-guru pengajarnya.
     */
    public function show(Subject $subject): JsonResponse
    {
        $subject->load('teachers');

        return response()->json([
            'success' => true,
            'data' => $subject,
        ]);
    }

    /**
     * Memperbarui data mata pelajaran.
     */
    public function update(Request $request, Subject $subject): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'code' => 'sometimes|string|max:10|unique:subjects,code,'.$subject->id,
            'description' => 'nullable|string',
        ]);

        $subject->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Mata pelajaran berhasil diperbarui',
            'data' => $subject,
        ]);
    }

    /**
     * Menghapus mata pelajaran.
     * Dilarang menghapus mapel yang masih ditugaskan ke guru.
     */
    public function destroy(Subject $subject): JsonResponse
    {
        if ($subject->teachers()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus mata pelajaran yang masih ditugaskan ke guru',
            ], 422);
        }

        $subject->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mata pelajaran berhasil dihapus',
        ]);
    }

    /**
     * Web (admin): daftar mata pelajaran.
     */
    public function webIndex(): View
    {
        $subjects = Subject::withCount('teacherSubjects')->orderBy('name')->paginate(50);

        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Web (admin): form tambah mapel.
     */
    public function webCreate(): View
    {
        return view('admin.subjects.create');
    }

    /**
     * Web (admin): simpan mapel baru.
     */
    public function webStore(StoreSubjectRequest $request): RedirectResponse
    {
        $data = $request->validated();
        Subject::create($data);
        ActivityLogger::log('create', 'Menambahkan mapel: '.$data['name'].' ('.$data['code'].')');

        return redirect()->route('admin.subjects.index')->with('success', 'Mapel berhasil ditambahkan');
    }

    /**
     * Web (admin): form edit mapel.
     */
    public function webEdit(Subject $subject): View
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    /**
     * Web (admin): perbarui mapel.
     */
    public function webUpdate(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $data = $request->validated();
        $subject->update($data);
        ActivityLogger::log('update', 'Mengubah mapel: '.$data['name'], $subject);

        return redirect()->route('admin.subjects.index')->with('success', 'Mapel berhasil diperbarui');
    }

    /**
     * Web (admin): hapus mapel.
     * Dilarang menghapus mapel yang masih dipetakan ke guru.
     */
    public function webDestroy(Subject $subject): RedirectResponse
    {
        if ($subject->teacherSubjects()->count() > 0) {
            return back()->withErrors(['Mapel masih memiliki pengajar']);
        }

        $name = $subject->name;
        $subject->delete();
        ActivityLogger::log('delete', 'Menghapus mapel: '.$name);

        return redirect()->route('admin.subjects.index')->with('success', 'Mapel berhasil dihapus');
    }
}
