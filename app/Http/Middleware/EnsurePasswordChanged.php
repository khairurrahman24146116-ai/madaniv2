<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Paksa user yang belum mengganti password default/awal (must_change_password = true)
     * untuk pergi ke halaman ganti password sebelum mengakses halaman lain.
     * Kecuali: halaman ganti password itu sendiri dan proses logout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('sanctum') ?? $request->user();

        if (! $user || ! $user->must_change_password) {
            return $next($request);
        }

        $route = $request->route();
        $routeName = $route?->getName();

        if (in_array($routeName, [
            'password.change',
            'password.change.update',
            'auth.logout.web',
            'auth.logout',
        ])) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda wajib mengganti password sebelum melanjutkan.',
                'must_change_password' => true,
            ], 403);
        }

        return redirect()->route('password.change');
    }
}
