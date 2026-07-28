<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Services\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LetterController extends Controller
{
    public function adminIndex(Request $request)
    {
        $letters = Letter::with('user')
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('letters.admin-index', compact('letters'));
    }

    public function create()
    {
        return view('letters.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'type' => 'required|in:pengumuman,edaran,surat_resmi,lainnya',
        ]);

        $letter = Letter::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'user_id' => $request->user()->id,
        ]);

        ActivityLogger::log('create', 'Membuat surat: '.$letter->title);

        return redirect()->route('admin.letters.index')->with('success', 'Surat berhasil dibuat');
    }

    public function edit(Letter $letter)
    {
        return view('letters.edit', compact('letter'));
    }

    public function update(Request $request, Letter $letter): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'type' => 'required|in:pengumuman,edaran,surat_resmi,lainnya',
            'is_published' => 'boolean',
        ]);

        $letter->update($validated);

        ActivityLogger::log('update', 'Mengubah surat: '.$letter->title);

        return redirect()->route('admin.letters.index')->with('success', 'Surat berhasil diperbarui');
    }

    public function destroy(Letter $letter): RedirectResponse
    {
        $title = $letter->title;
        $letter->delete();

        ActivityLogger::log('delete', 'Menghapus surat: '.$title);

        return redirect()->route('admin.letters.index')->with('success', 'Surat berhasil dihapus');
    }

    public function show(Letter $letter)
    {
        return view('letters.show', compact('letter'));
    }

    public function guruIndex()
    {
        $letters = Letter::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('letters.guru-index', compact('letters'));
    }

    public function waliIndex()
    {
        $letters = Letter::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('letters.wali-index', compact('letters'));
    }

    public function printPdf(Letter $letter)
    {
        $pdf = Pdf::loadView('pdf.letter', [
            'letter' => $letter,
            'school' => 'SMA Dayah Madani Al-Aziziyah',
        ]);

        $filename = 'surat_'.str_replace(' ', '_', $letter->title).'.pdf';

        return $pdf->download($filename);
    }
}
