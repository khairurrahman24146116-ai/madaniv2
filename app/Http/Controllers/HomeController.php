<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Redirect pengguna yang sudah login ke dashboard sesuai peran.
     * Jika belum login, tampilkan halaman beranda publik.
     */
    public function redirectHome(): RedirectResponse|View
    {
        if (auth()->check()) {
            return redirect()->route($this->dashboardRouteFor(auth()->user()));
        }

        return view('welcome');
    }

    /**
     * Halaman login web.
     * Jika sudah login, redirect ke dashboard sesuai peran.
     */
    public function login(): RedirectResponse|View
    {
        if (auth()->check()) {
            return redirect()->route($this->dashboardRouteFor(auth()->user()));
        }

        return view('auth.login');
    }

    private function dashboardRouteFor(mixed $user): string
    {
        return match (true) {
            $user->isWaliMurid() => 'wali-murid.dashboard',
            $user->isBendahara() => 'bendahara.dashboard',
            $user->isAdmin() => 'admin.dashboard',
            default => 'dashboard',
        };
    }
}
