@extends('layouts.app')

@section('content')
<x-page-header title="Kelola Pengguna" subtitle="Manajemen akun guru, admin, dan wali murid" icon="manage_accounts" />

{{-- Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 text-center">
        <p class="text-headline-xl text-on-surface font-bold">{{ $totalUsers }}</p>
        <p class="text-caption text-on-surface-variant mt-1">Total Akun</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 text-center">
        <p class="text-headline-xl text-primary font-bold">{{ $totalGuru }}</p>
        <p class="text-caption text-on-surface-variant mt-1">Guru</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 text-center">
        <p class="text-headline-xl text-secondary font-bold">{{ $totalWaliMurid }}</p>
        <p class="text-caption text-on-surface-variant mt-1">Wali Murid</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 text-center">
        <p class="text-headline-xl text-tertiary-container font-bold">{{ $totalBendahara }}</p>
        <p class="text-caption text-on-surface-variant mt-1">Bendahara</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 text-center">
        <p class="text-headline-xl text-on-surface-variant font-bold">{{ $totalAdmin }}</p>
        <p class="text-caption text-on-surface-variant mt-1">Admin</p>
    </div>
</div>

{{-- Filters --}}
<form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap gap-4 mb-6 p-4 bg-surface-container-low rounded-xl border border-outline-variant">
    <div>
        <label class="text-label-md text-on-surface-variant block mb-1">ROLE</label>
        <select name="role" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md">
            <option value="">Semua Role</option>
            <option value="admin" @selected(request('role') === 'admin')>Admin</option>
            <option value="bendahara" @selected(request('role') === 'bendahara')>Bendahara</option>
            <option value="guru" @selected(request('role') === 'guru')>Guru</option>
            <option value="wali_murid" @selected(request('role') === 'wali_murid')>Wali Murid</option>
        </select>
    </div>
    <div>
        <label class="text-label-md text-on-surface-variant block mb-1">CARI</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email..." class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-60">
    </div>
    <div class="self-end flex gap-2">
        <x-button type="submit" variant="primary" icon="filter_list">Filter</x-button>
        <a href="{{ route('admin.users.index') }}" class="px-6 py-2 border border-outline-variant rounded-lg text-label-md text-on-surface-variant hover:bg-surface-container transition-colors inline-flex items-center">
            Reset
        </a>
    </div>
</form>

{{-- Table --}}
<x-card variant="default">
    <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
        <h3 class="text-headline-md text-on-surface">Data Pengguna</h3>
        <div class="flex items-center gap-4">
            <span class="text-caption text-on-surface-variant">{{ $users->total() }} records</span>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-on-primary text-label-md font-semibold hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-[18px]">person_add</span> Tambah Akun
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Nama</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Email</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Role</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-center">Aktif</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($users as $user)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container font-bold shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-body-md text-on-surface font-semibold">{{ $user->name }}</p>
                                <p class="text-caption text-on-surface-variant">Terdaftar {{ $user->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @php
                        $roleBadge = [
                            'admin' => 'bg-primary-fixed/40 text-on-primary-fixed  ',
                            'bendahara' => 'bg-tertiary-fixed/50 text-on-tertiary-fixed-variant  ',
                            'guru' => 'bg-secondary-fixed/60 text-on-secondary-fixed-variant  ',
                            'wali_murid' => 'bg-secondary-fixed/60 text-on-secondary-fixed-variant  ',
                        ];
                        $roleLabel = ['admin' => 'Admin', 'bendahara' => 'Bendahara', 'guru' => 'Guru', 'wali_murid' => 'Wali Murid'];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $roleBadge[$user->role] ?? 'bg-surface-container text-on-surface-variant' }}">{{ $roleLabel[$user->role] ?? $user->role }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($user->is_active)
                        <span class="text-tertiary-container material-symbols-outlined text-[18px]">check_circle</span>
                        @else
                        <span class="text-on-surface-variant material-symbols-outlined text-[18px]">cancel</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-4">
                            @unless($user->id === auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}"
                                data-confirm="{{ $user->is_active ? 'Nonaktifkan akun '.$user->name.'?' : 'Aktifkan akun '.$user->name.'?' }}"
                                data-confirm-title="{{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                data-confirm-variant="{{ $user->is_active ? 'warning' : 'info' }}"
                                data-confirm-confirm-text="{{ $user->is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}"
                                class="inline-flex items-center">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 text-label-md {{ $user->is_active ? 'text-error hover:text-error/80' : 'text-primary hover:text-primary/80' }}">
                                    <span class="material-symbols-outlined text-[18px]">{{ $user->is_active ? 'person_off' : 'person' }}</span> {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            @endunless
                            <button type="button" onclick="openResetModal({{ $user->id }}, '{{ $user->name }}')" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80">
                                <span class="material-symbols-outlined text-[18px]">key</span> Reset Password
                            </button>
                            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80">
                                <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                            </a>
                            @unless($user->id === auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                data-confirm="Hapus akun {{ $user->name }}? Tindakan ini tidak dapat dibatalkan."
                                data-confirm-title="Hapus Akun"
                                data-confirm-variant="danger"
                                data-confirm-confirm-text="Ya, Hapus"
                                class="inline-flex items-center">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 text-label-md text-error hover:text-error/80">
                                    <span class="material-symbols-outlined text-[18px]">delete</span> Hapus
                                </button>
                            </form>
                            @endunless
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-on-surface-variant">Belum ada data pengguna.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($users, 'links'))
    <div class="p-6 border-t border-outline-variant">
        {{ $users->links() }}
    </div>
    @endif
</x-card>

{{-- Reset Password Modal --}}
<div id="reset-modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-[420px] bg-surface-container-lowest rounded-lg shadow-[0_10px_25px_rgba(0,0,0,0.15)] border border-outline-variant">
            <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
                <h2 class="text-headline-md font-semibold text-on-surface">Reset Password</h2>
                <button type="button" onclick="closeResetModal()" class="p-1.5 rounded-lg hover:bg-surface-container-high transition-colors text-on-surface-variant" aria-label="Tutup">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <form id="reset-form" method="POST" class="p-6">
                @csrf
                <p class="text-body-md text-on-surface-variant mb-6">Reset password untuk: <strong id="reset-user-name" class="text-on-surface"></strong></p>

                <div class="mb-6">
                    <label class="text-label-md text-on-surface-variant block mb-1.5">PASSWORD BARU</label>
                    <div class="relative">
                        <input type="password" name="password" id="reset-password" required minlength="6" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface h-11 px-3 text-body-md w-full pr-10 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition" placeholder="Minimal 6 karakter">
                        <button type="button" onclick="togglePassword('reset-password', this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="text-label-md text-on-surface-variant block mb-1.5">KONFIRMASI PASSWORD</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="reset-password-confirm" required minlength="6" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface h-11 px-3 text-body-md w-full pr-10 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition" placeholder="Ketik ulang password">
                        <button type="button" onclick="togglePassword('reset-password-confirm', this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:items-center gap-3">
                    <button type="button" onclick="generatePassword()" class="inline-flex items-center justify-center gap-1.5 h-11 px-4 border border-outline-variant rounded-lg text-body-md text-on-surface-variant hover:bg-surface-container transition-colors w-full sm:w-auto sm:me-auto">
                        <span class="material-symbols-outlined text-[18px]">autorenew</span> Generate
                    </button>
                    <div class="flex flex-col-reverse sm:flex-row gap-3 w-full sm:w-auto sm:items-center">
                        <button type="button" onclick="closeResetModal()" class="inline-flex items-center justify-center h-11 px-4 border border-outline-variant rounded-lg text-body-md text-on-surface-variant hover:bg-surface-container transition-colors w-full sm:w-auto">Batal</button>
                        <x-button variant="primary" type="submit" icon="key" class="w-full sm:w-auto justify-center !h-11 !px-4">Simpan Password</x-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openResetModal(userId, userName) {
        document.getElementById('reset-user-name').textContent = userName;
        document.getElementById('reset-form').action = '{{ url("/app/admin/users") }}/' + userId + '/reset-password';
        document.getElementById('reset-password').value = '';
        document.getElementById('reset-password-confirm').value = '';
        document.getElementById('reset-modal').classList.remove('hidden');
    }
    function closeResetModal() {
        document.getElementById('reset-modal').classList.add('hidden');
    }
    function togglePassword(inputId, btn) {
        var input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.querySelector('.material-symbols-outlined').textContent = 'visibility_off';
        } else {
            input.type = 'password';
            btn.querySelector('.material-symbols-outlined').textContent = 'visibility';
        }
    }
    function generatePassword() {
        var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#!$%&*';
        var pwd = '';
        for (var i = 0; i < 12; i++) {
            pwd += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('reset-password').value = pwd;
        document.getElementById('reset-password-confirm').value = pwd;
    }
    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('reset-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal || e.target === modal.children[0] || e.target === modal.children[1]) {
                    closeResetModal();
                }
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeResetModal();
            });
        }
    });
</script>
@endpush
@endsection
