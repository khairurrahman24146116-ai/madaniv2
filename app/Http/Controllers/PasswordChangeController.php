<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordChangeController extends Controller
{
    public function showForm()
    {
        return view('auth.change-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user('sanctum') ?? $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini salah.'],
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ])->save();

        ActivityLogger::log('update', 'Mengganti password (wajib ganti)');

        $redirectRoute = match (true) {
            $user->isWaliMurid() => 'wali-murid.dashboard',
            $user->isAdmin() => 'admin.dashboard',
            default => 'dashboard',
        };

        return redirect()->route($redirectRoute)->with('success', 'Password berhasil diganti. Silakan lanjutkan aktivitas Anda.');
    }
}
