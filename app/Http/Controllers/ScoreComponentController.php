<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScoreComponentRequest;
use App\Http\Requests\UpdateScoreComponentRequest;
use App\Models\ScoreComponent;
use App\Models\Subject;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller ScoreComponent — Konfigurasi bobot komponen penilaian (Admin).
 *
 * FR-3.2: Admin mengatur persentase bobot Tugas, PH, UTS, UAS
 * per mata pelajaran per semester. Digunakan untuk kalkulasi NA otomatis.
 */
class ScoreComponentController extends Controller
{
    /**
     * Menampilkan daftar bobot komponen.
     * Filter: subject_id, semester, academic_year.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ScoreComponent::with('subject');

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $components = $query->orderBy('subject_id')->orderBy('code')->get();

        return response()->json([
            'success' => true,
            'data' => $components,
        ]);
    }

    /**
     * Menambah atau memperbarui bobot komponen.
     * Jika kombinasi (subject_id, code, semester, academic_year) sudah ada, bobot diupdate.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'code' => 'required|in:tugas,ph,uts,uas',
            'name' => 'required|string|max:50',
            'weight' => 'required|numeric|min:0|max:100',
            'semester' => 'required|in:ganjil,genap',
            'academic_year' => 'required|string|max:9',
        ]);

        // Hitung total bobot saat ini (kecuali komponen yang sama jika update)
        $existingTotal = ScoreComponent::where('subject_id', $validated['subject_id'])
            ->where('semester', $validated['semester'])
            ->where('academic_year', $validated['academic_year'])
            ->where('code', '!=', $validated['code'])
            ->sum('weight');

        if (($existingTotal + (float) $validated['weight']) > 100) {
            return response()->json([
                'success' => false,
                'message' => "Total bobot tidak boleh melebihi 100%. Saat ini {$existingTotal}%, ditambah {$validated['weight']}% menjadi ".($existingTotal + (float) $validated['weight']).'%',
            ], 422);
        }

        $component = ScoreComponent::updateOrCreate(
            [
                'subject_id' => $validated['subject_id'],
                'code' => $validated['code'],
                'semester' => $validated['semester'],
                'academic_year' => $validated['academic_year'],
            ],
            $validated
        );

        $component->load('subject');

        return response()->json([
            'success' => true,
            'message' => 'Bobot komponen berhasil disimpan',
            'data' => $component,
        ], 201);
    }

    /**
     * Menampilkan detail bobot komponen.
     */
    public function show(ScoreComponent $scoreComponent): JsonResponse
    {
        $scoreComponent->load('subject');

        return response()->json([
            'success' => true,
            'data' => $scoreComponent,
        ]);
    }

    /**
     * Memperbarui bobot komponen.
     */
    public function update(Request $request, ScoreComponent $scoreComponent): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:50',
            'weight' => 'sometimes|numeric|min:0|max:100',
        ]);

        if (isset($validated['weight'])) {
            $existingTotal = ScoreComponent::where('subject_id', $scoreComponent->subject_id)
                ->where('semester', $scoreComponent->semester)
                ->where('academic_year', $scoreComponent->academic_year)
                ->where('id', '!=', $scoreComponent->id)
                ->sum('weight');

            if (($existingTotal + (float) $validated['weight']) > 100) {
                return response()->json([
                    'success' => false,
                    'message' => "Total bobot tidak boleh melebihi 100%. Saat ini {$existingTotal}%, ditambah {$validated['weight']}% menjadi ".($existingTotal + (float) $validated['weight']).'%',
                ], 422);
            }
        }

        $scoreComponent->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bobot komponen berhasil diperbarui',
            'data' => $scoreComponent->fresh()->load('subject'),
        ]);
    }

    /**
     * Menghapus bobot komponen.
     */
    public function destroy(ScoreComponent $scoreComponent): JsonResponse
    {
        $scoreComponent->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bobot komponen berhasil dihapus',
        ]);
    }

    /**
     * Web (admin): daftar bobot komponen nilai.
     */
    public function webIndex(): View
    {
        $components = ScoreComponent::with('subject')->orderBy('subject_id')->paginate(50);

        return view('admin.score-components.index', compact('components'));
    }

    /**
     * Web (admin): form tambah bobot komponen.
     */
    public function webCreate(): View
    {
        $subjects = Subject::orderBy('name')->get();

        return view('admin.score-components.create', compact('subjects'));
    }

    /**
     * Web (admin): simpan bobot komponen baru.
     */
    public function webStore(StoreScoreComponentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        ScoreComponent::create($data);
        ActivityLogger::log('create', 'Menambahkan bobot nilai: '.$data['name'].' ('.$data['code'].')');

        return redirect()->route('admin.score-components.index')->with('success', 'Bobot nilai berhasil ditambahkan');
    }

    /**
     * Web (admin): form edit bobot komponen.
     */
    public function webEdit(ScoreComponent $scoreComponent): View
    {
        $subjects = Subject::orderBy('name')->get();

        return view('admin.score-components.edit', compact('scoreComponent', 'subjects'));
    }

    /**
     * Web (admin): perbarui bobot komponen.
     */
    public function webUpdate(UpdateScoreComponentRequest $request, ScoreComponent $scoreComponent): RedirectResponse
    {
        $data = $request->validated();
        $scoreComponent->update($data);
        ActivityLogger::log('update', 'Mengubah bobot nilai: '.$data['name'], $scoreComponent);

        return redirect()->route('admin.score-components.index')->with('success', 'Bobot nilai berhasil diperbarui');
    }

    /**
     * Web (admin): hapus bobot komponen.
     */
    public function webDestroy(ScoreComponent $scoreComponent): RedirectResponse
    {
        $name = $scoreComponent->name;
        $scoreComponent->delete();
        ActivityLogger::log('delete', 'Menghapus bobot nilai: '.$name);

        return redirect()->route('admin.score-components.index')->with('success', 'Bobot nilai berhasil dihapus');
    }
}
