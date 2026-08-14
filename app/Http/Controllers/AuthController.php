<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda dinonaktifkan',
            ], 403);
        }

        $token = $user->createToken('madani-al-aziziyah', [$user->role])->plainTextToken;

        ActivityLogger::log('login', 'Login API: '.$user->name);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user->only(['id', 'name', 'email', 'role', 'must_change_password']),
                'token' => $token,
            ],
        ]);
    }

    public function loginWeb(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($validated)) {
            $user = Auth::user();
            if (! $user->is_active) {
                Auth::logout();

                return back()->withErrors(['email' => 'Akun Anda dinonaktifkan']);
            }

            ActivityLogger::log('login', 'Login web: '.$user->name);

            $redirectRoute = match (true) {
                $user->isWaliMurid() => 'wali-murid.dashboard',
                $user->isBendahara() => 'bendahara.dashboard',
                $user->isAdmin() => 'admin.dashboard',
                default => 'dashboard',
            };

            return redirect()->intended(route($redirectRoute));
        }

        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    public function logout(Request $request): JsonResponse
    {
        ActivityLogger::log('logout', 'Logout API: '.$request->user()->name);
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }

    public function logoutWeb(Request $request)
    {
        ActivityLogger::log('logout', 'Logout web: '.auth()->user()?->name);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['teacherSubjects.subject', 'teacherSubjects.classroom', 'students']);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }
}
