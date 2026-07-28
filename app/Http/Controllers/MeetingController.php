<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function waliIndex(Request $request)
    {
        $meetings = Meeting::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('meetings.wali-index', compact('meetings'));
    }

    public function waliCreate()
    {
        return view('meetings.wali-create');
    }

    public function waliStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|max:255',
            'description' => 'required',
            'requested_date' => 'required|date|after_or_equal:today',
        ]);

        Meeting::create([
            'user_id' => $request->user()->id,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'requested_date' => $validated['requested_date'],
        ]);

        ActivityLogger::log('create', 'Permintaan pertemuan: '.$validated['subject']);

        return redirect()->route('wali.meetings.index')->with('success', 'Permintaan pertemuan berhasil dikirim');
    }

    public function adminIndex(Request $request)
    {
        $query = Meeting::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $meetings = $query->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('meetings.admin-index', compact('meetings'));
    }

    public function adminShow(Meeting $meeting)
    {
        $meeting->load('user');

        return view('meetings.admin-show', compact('meeting'));
    }

    public function adminApprove(Request $request, Meeting $meeting): RedirectResponse
    {
        $meeting->update([
            'status' => 'approved',
            'admin_id' => $request->user()->id,
            'responded_at' => now(),
        ]);

        ActivityLogger::log('update', 'Menyetujui pertemuan: '.$meeting->subject);

        return redirect()->route('admin.meetings.index')->with('success', 'Pertemuan disetujui');
    }

    public function adminReject(Request $request, Meeting $meeting): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|max:500',
        ]);

        $meeting->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'admin_id' => $request->user()->id,
            'responded_at' => now(),
        ]);

        ActivityLogger::log('update', 'Menolak pertemuan: '.$meeting->subject);

        return redirect()->route('admin.meetings.index')->with('success', 'Pertemuan ditolak');
    }
}
