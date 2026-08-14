@extends('layouts.app')

@section('content')
<x-page-header title="Tambah Akun" subtitle="Buat akun baru untuk guru, wali murid, admin, atau bendahara" icon="person_add"
    :actions="[['label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('admin.users.index')]]" />

<x-card variant="default" padding="lg" class="max-w-2xl">
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-1">NAMA LENGKAP</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                        placeholder="Ahmad Fauzi">
                    @error('name')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-1">ROLE</label>
                    <select name="role" required
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                        <option value="">Pilih role</option>
                        <option value="guru" @selected(old('role')=='guru')>Guru</option>
                        <option value="wali_murid" @selected(old('role')=='wali_murid')>Wali Murid</option>
                        <option value="bendahara" @selected(old('role')=='bendahara')>Bendahara</option>
                        <option value="admin" @selected(old('role')=='admin')>Admin</option>
                    </select>
                    @error('role')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">EMAIL</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                    placeholder="nama@email.com">
                @error('email')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-1">NO. TELP</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('phone')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-1">ALAMAT</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-1">PASSWORD</label>
                    <div class="relative">
                        <input type="password" name="password" required minlength="6"
                            class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors pr-12"
                            placeholder="Minimal 6 karakter">
                        <button type="button" onclick="toggleCreatePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface">
                            <span class="material-symbols-outlined text-[20px]" id="create-pass-icon">visibility</span>
                        </button>
                    </div>
                    @error('password')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-1">KONFIRMASI PASSWORD</label>
                    <input type="password" name="password_confirmation" required minlength="6"
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                        placeholder="Ketik ulang password">
                </div>
            </div>
            <div class="flex gap-4 pt-4 border-t border-outline-variant">
                <x-button variant="primary" type="submit" icon="save">Simpan Akun</x-button>
                <x-button variant="outline" href="{{ route('admin.users.index') }}">Batal</x-button>
            </div>
        </div>
    </form>
</x-card>
@endsection

@push('scripts')
<script>
    function toggleCreatePassword() {
        var input = document.querySelector('input[name="password"]');
        var icon = document.getElementById('create-pass-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }
</script>
@endpush