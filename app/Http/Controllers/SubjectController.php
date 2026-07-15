<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Subject::withCount('teachers');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $subjects = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $subjects,
        ]);
    }

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

    public function show(Subject $subject): JsonResponse
    {
        $subject->load('teachers');

        return response()->json([
            'success' => true,
            'data' => $subject,
        ]);
    }

    public function update(Request $request, Subject $subject): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'code' => 'sometimes|string|max:10|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
        ]);

        $subject->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Mata pelajaran berhasil diperbarui',
            'data' => $subject,
        ]);
    }

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
}
