<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\TandaTangan;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Web: form edit profil pengguna yang sedang login.
     */
    public function edit(): View
    {
        $user = auth()->user();
        $ttdAktif = match (true) {
            $user->isAdmin() => TandaTangan::where('role', 'kepala_sekolah')->where('is_active', true)->latest('id')->first(),
            $user->isGuru() => TandaTangan::where('user_id', $user->id)->where('role', 'wali_kelas')->where('is_active', true)->latest('id')->first(),
            default => null,
        };
        $ttdRoleLabel = $user->isAdmin() ? 'Kepala Sekolah' : ($user->isGuru() ? 'Wali Kelas' : null);

        return view('profile.edit', compact('ttdAktif', 'ttdRoleLabel'));
    }

    /**
     * Web: simpan perubahan profil pengguna.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = auth()->user();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->address = $data['address'] ?? null;

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        if ($request->boolean('remove_photo') && $user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
        }

        $user->save();

        $this->updateTandaTangan($request, $user);

        ActivityLogger::log('update', 'Memperbarui profil: '.$user->name);

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Kelola tanda tangan digital: admin mengunggah TTD Kepala Sekolah,
     * guru mengunggah TTD wali kelas. Hanya satu TTD aktif per peran/user;
     * unggahan baru menonaktifkan yang lama.
     */
    private function updateTandaTangan(UpdateProfileRequest $request, $user): void
    {
        if ($user->isAdmin()) {
            $role = 'kepala_sekolah';
            $ownerId = null;
            $query = TandaTangan::where('role', 'kepala_sekolah');
        } elseif ($user->isGuru()) {
            $role = 'wali_kelas';
            $ownerId = $user->id;
            $query = TandaTangan::where('user_id', $user->id)->where('role', 'wali_kelas');
        } else {
            return;
        }

        $label = $role === 'kepala_sekolah' ? 'Kepala Sekolah' : 'Wali Kelas';

        if ($request->hasFile('tanda_tangan')) {
            $query->where('is_active', true)->update(['is_active' => false]);

            $tandaTangan = new TandaTangan([
                'user_id' => $ownerId,
                'role' => $role,
                'file_path' => $request->file('tanda_tangan')->store('tanda-tangan', 'public'),
                'is_active' => true,
            ]);
            $tandaTangan->save();

            ActivityLogger::log('update', 'Mengunggah tanda tangan '.$label);
        } elseif ($request->boolean('hapus_tanda_tangan')) {
            $query->where('is_active', true)->update(['is_active' => false]);

            ActivityLogger::log('update', 'Menghapus tanda tangan '.$label);
        }
    }
}
