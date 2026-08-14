@extends('layouts.app')

@section('content')
<x-page-header title="Edit Akun" subtitle="{{ $user->name }} ({{ $user->email }})" icon="manage_accounts"
    :actions="[['label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('admin.users.index')]]" />

<x-card variant="default" padding="lg" class="max-w-2xl">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf @method('PUT')
        <div class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-1">NAMA LENGKAP</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('name')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-1">ROLE</label>
                    <select name="role" required
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                        <option value="guru" @selected(old('role', $user->role)=='guru')>Guru</option>
                        <option value="wali_murid" @selected(old('role', $user->role)=='wali_murid')>Wali Murid</option>
                        <option value="bendahara" @selected(old('role', $user->role)=='bendahara')>Bendahara</option>
                        <option value="admin" @selected(old('role', $user->role)=='admin')>Admin</option>
                    </select>
                    @error('role')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">EMAIL</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('email')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-1">NO. TELP</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('phone')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-1">ALAMAT</label>
                    <input type="text" name="address" value="{{ old('address', $user->address) }}"
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                </div>
            </div>
            <div class="flex gap-4 pt-4 border-t border-outline-variant">
                <x-button variant="primary" type="submit" icon="save">Simpan Perubahan</x-button>
                <x-button variant="outline" href="{{ route('admin.users.index') }}">Batal</x-button>
            </div>
        </div>
    </form>
</x-card>
@endsection