@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-surface rounded-2xl border border-outline-variant overflow-hidden shadow-sm">
        <div class="border-b border-outline-variant p-6 bg-warning-container/50  flex items-start gap-3">
            <span class="material-symbols-outlined text-warning shrink-0 mt-0.5">warning</span>
            <p class="text-body-md text-on-warning-container ">
                <strong>Catat password ini sekarang.</strong> Password hanya ditampilkan sekali ini dan tidak akan muncul lagi. Simpan di tempat aman lalu teruskan ke {{ $userName }} jika perlu.
            </p>
        </div>

        <div class="p-6 space-y-6">
            <p class="text-body-md text-on-surface">
                Password akun <strong class="text-on-surface">{{ $userName }}</strong> berhasil di-reset.
                Pengguna wajib mengganti password saat login berikutnya.
            </p>

            <div class="flex items-center gap-2">
                <span id="revealed-password" class="font-mono text-on-surface bg-surface-container-low rounded-md border border-outline-variant px-4 py-2 text-[16px] tracking-wider select-all flex-1 break-all">{{ $password }}</span>
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('revealed-password').textContent);this.textContent='Disalin!';setTimeout(()=>this.textContent='Salin',1600)" class="inline-flex items-center gap-1 rounded-md bg-primary text-on-primary px-4 h-11 text-label-md hover:bg-primary/90 transition-colors shrink-0">Salin</button>
            </div>

            <p class="text-caption text-on-surface-variant">
                Halaman ini tidak bisa di-refresh lagi. Jika Anda me-refresh atau menutup halaman, password tidak akan ditampilkan ulang.
            </p>

            <div>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-md bg-primary text-on-primary px-6 h-11 text-label-md hover:bg-primary/90 transition-colors">
                    Selesai
                </a>
            </div>
        </div>
    </div>
</div>
@endsection