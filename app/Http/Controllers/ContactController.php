<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function waliIndex(Request $request)
    {
        $messages = ContactMessage::with('user')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('contact.wali-index', compact('messages'));
    }

    public function waliCreate()
    {
        return view('contact.wali-create');
    }

    public function waliStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|max:255',
            'message' => 'required',
        ]);

        ContactMessage::create([
            'user_id' => $request->user()->id,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        ActivityLogger::log('create', 'Pesan ke kepala sekolah: '.$validated['subject']);

        return redirect()->route('wali.contact.index')->with('success', 'Pesan berhasil dikirim');
    }

    public function adminIndex(Request $request)
    {
        $query = ContactMessage::with('user');

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', "%{$request->search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$request->search}%"));
            });
        }

        $messages = $query->orderBy('is_read')->orderBy('created_at', 'desc')->paginate(20);

        return view('contact.admin-index', compact('messages'));
    }

    public function adminShow(ContactMessage $contactMessage)
    {
        if (! $contactMessage->is_read) {
            $contactMessage->update(['is_read' => true]);
        }

        $contactMessage->load('user');

        return view('contact.admin-show', compact('contactMessage'));
    }

    public function adminReply(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $validated = $request->validate([
            'admin_reply' => 'required',
        ]);

        $contactMessage->update([
            'admin_reply' => $validated['admin_reply'],
            'replied_at' => now(),
        ]);

        ActivityLogger::log('update', 'Membalas pesan: '.$contactMessage->subject);

        return redirect()->route('admin.contact.index')->with('success', 'Balasan berhasil dikirim');
    }
}
