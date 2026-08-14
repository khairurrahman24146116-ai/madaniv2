<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Web (admin): riwayat aktivitas pengguna.
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')
            ->select(['id', 'user_id', 'action', 'description', 'ip_address', 'created_at'])
            ->latest();

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }

        $logs = $query->paginate(50);

        return view('admin.activity-logs.index', compact('logs'));
    }
}
