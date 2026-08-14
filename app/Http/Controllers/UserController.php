<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetUserPasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\ActiveLetterRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Web (admin): daftar pengguna dengan filter & ringkasan.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(50);

        $totalUsers = User::count();
        $totalGuru = User::where('role', 'guru')->count();
        $totalWaliMurid = User::where('role', 'wali_murid')->count();
        $totalAdmin = User::where('role', 'admin')->count();
        $totalBendahara = User::where('role', 'bendahara')->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'totalGuru', 'totalWaliMurid', 'totalAdmin', 'totalBendahara'));
    }

    /**
     * Web (admin): tampilkan form tambah akun.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Web (admin): simpan akun baru dengan password sekali pakai.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'is_active' => true,
            'must_change_password' => true,
        ]);

        ActivityLogger::log('create', 'Menambahkan akun: '.$user->name.' ('.$user->email.', role '.$user->role.')');

        // Password default sekali pakai — konsisten dengan alur reset password.
        session()->put('pending_reset_password', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'password' => $data['password'],
        ]);

        return redirect()->route('admin.users.password-reveal', $user);
    }

    /**
     * Web (admin): tampilkan form edit akun.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Web (admin): perbarui data akun.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        // Guard: role guru yang masih memiliki mapping mapel tidak boleh diganti role.
        if ($user->role === 'guru' && $data['role'] !== 'guru' && $user->teacherSubjects()->exists()) {
            return back()->with('error', 'Tidak dapat mengubah role: guru ini masih memiliki mapping mata pelajaran.');
        }

        // Guard: role wali murid yang masih terhubung ke siswa tidak boleh diganti role.
        if ($user->role === 'wali_murid' && $data['role'] !== 'wali_murid' && $user->students()->exists()) {
            return back()->with('error', 'Tidak dapat mengubah role: wali murid ini masih terhubung ke data siswa.');
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        ActivityLogger::log('update', 'Mengubah akun: '.$user->name.' ('.$user->email.')');

        return redirect()->route('admin.users.index')->with('success', 'Akun '.$user->name.' berhasil diperbarui.');
    }

    /**
     * Web (admin): hapus akun dengan guard (diri sendiri, admin aktif terakhir, data terkait).
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        if ($user->role === 'admin' && $user->is_active) {
            $hasOtherActiveAdmin = User::where('role', 'admin')
                ->where('is_active', true)
                ->whereNotIn('id', [auth()->id(), $user->id])
                ->exists();

            if (! $hasOtherActiveAdmin) {
                return back()->with('error', 'Minimal satu admin aktif lainnya harus tersisa.');
            }
        }

        $related = [];

        if ($user->students()->exists()) {
            $related[] = 'data siswa';
        }

        if ($user->teacherSubjects()->exists()) {
            $related[] = 'mapping mata pelajaran';
        }

        if ($user->teacherAttendances()->exists()) {
            $related[] = 'riwayat absensi guru';
        }

        if ($user->letters()->exists()) {
            $related[] = 'surat';
        }

        if ($user->contactMessages()->exists()) {
            $related[] = 'pesan masuk';
        }

        if ($user->meetings()->exists()) {
            $related[] = 'permintaan pertemuan';
        }

        if (ActiveLetterRequest::where('teacher_id', $user->id)->exists()) {
            $related[] = 'surat aktif';
        }

        if (! empty($related)) {
            return back()->with('error', 'Akun tidak dapat dihapus karena masih terkait dengan '.implode(', ', $related).'. Nonaktifkan akun sebagai alternatif.');
        }

        $name = $user->name;
        $email = $user->email;

        $user->delete();

        ActivityLogger::log('delete', 'Menghapus akun: '.$name.' ('.$email.')');

        return redirect()->route('admin.users.index')->with('success', 'Akun '.$name.' berhasil dihapus.');
    }

    /**
     * Web (admin): reset password akun menjadi password sekali pakai.
     */
    public function resetPassword(ResetUserPasswordRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $plainPassword = $data['password'];
        $user->update([
            'password' => bcrypt($plainPassword),
            'must_change_password' => true,
        ]);

        ActivityLogger::log('update', 'Reset password: '.$user->name.' ('.$user->email.')');

        // Simpan password SEKALI pakai saja (single-use). Hilang setelah ditampilkan.
        session()->put('pending_reset_password', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'password' => $plainPassword,
        ]);

        return redirect()->route('admin.users.password-reveal', $user);
    }

    /**
     * Web (admin): aktifkan/nonaktifkan akun pengguna.
     */
    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat mengubah status akun sendiri.');
        }

        $wasActive = $user->is_active;

        if ($wasActive && $user->role === 'admin') {
            $hasOtherActiveAdmin = User::where('role', 'admin')
                ->where('is_active', true)
                ->whereNotIn('id', [auth()->id(), $user->id])
                ->exists();

            if (! $hasOtherActiveAdmin) {
                return back()->with('error', 'Minimal satu admin aktif lainnya harus tersisa.');
            }
        }

        $user->update(['is_active' => ! $wasActive]);

        ActivityLogger::log('update', ($wasActive ? 'Nonaktifkan' : 'Aktifkan').' akun: '.$user->name.' ('.$user->email.')');

        return back()->with('success', 'Status akun '.$user->name.' diubah menjadi '.($wasActive ? 'nonaktif' : 'aktif').'.');
    }

    /**
     * Web (admin): tampilkan password sekali pakai hasil reset.
     * Password diambil dari session single-use lalu dihapus, sehingga
     * refresh/back/tebus tidak akan menampilkan lagi.
     */
    public function passwordReveal(User $user): View|RedirectResponse
    {
        $pending = session()->pull('pending_reset_password');

        if ($pending === null || (int) $pending['user_id'] !== (int) $user->id) {
            return redirect()->route('admin.users.index');
        }

        return view('admin.users.password-reveal', [
            'userName' => $pending['user_name'],
            'password' => $pending['password'],
        ]);
    }
}
