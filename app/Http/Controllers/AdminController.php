<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Web (admin): dashboard utama admin.
     */
    public function dashboard(): View
    {
        return view('admin.dashboard');
    }
}
