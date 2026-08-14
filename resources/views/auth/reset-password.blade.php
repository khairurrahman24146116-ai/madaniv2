@extends('layouts.guest')

@section('content')
<div class="bg-surface-container-lowest rounded-xl shadow-lg border border-surface-container-high overflow-hidden">
    {{-- Header Section --}}
    <div class="bg-primary-container p-8 flex flex-col items-center justify-center text-center rounded-t-xl">
        <div class="w-16 h-16 bg-surface-container-lowest rounded-full flex items-center justify-center mb-4 shadow-sm">
            <span class="material-symbols-outlined text-[32px] text-primary" style="font-variation-settings: 'FILL' 1;">lock_reset</span>
        </div>
        <h1 class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-on-primary-container font-bold">
            Reset Password
        </h1>
        <p class="font-title-lg text-title-lg text-primary-fixed-dim opacity-90 mt-1">
            Buat password baru untuk akun Anda
        </p>
    </div>

    {{-- Form Section --}}
    <div class="p-8">
        @if($errors->any())
            <div class="mb-6 p-4 bg-error-container text-on-error-container rounded-lg text-body-md flex items-start gap-3 border border-error/30" role="alert">
                <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5">error</span>
                <div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="space-y-2">
                <label class="block font-label-mono text-label-mono text-on-surface-variant" for="email">
                    Email
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">
                        mail
                    </span>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                           placeholder="admin@madani.id"
                           class="w-full pl-10 pr-4 py-3 bg-surface-variant border-0 border-b border-outline-variant focus:border-primary focus:ring-0 focus:outline-none rounded-t-lg transition-colors font-data-table text-data-table text-on-surface">
                </div>
                @error('email')
                    <p class="font-label-mono text-label-mono text-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label class="block font-label-mono text-label-mono text-on-surface-variant" for="password">
                    Password Baru
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">
                        lock
                    </span>
                    <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"
                           placeholder="Minimal 8 karakter"
                           class="w-full pl-10 pr-10 py-3 bg-surface-variant border-0 border-b border-outline-variant focus:border-primary focus:ring-0 focus:outline-none rounded-t-lg transition-colors font-data-table text-data-table text-on-surface">
                    <button type="button"
                            onclick="togglePassword('password', 'visibilityIconPassword')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface transition-colors"
                            aria-label="Tampilkan/sembunyikan password">
                        <span class="material-symbols-outlined" id="visibilityIconPassword">visibility</span>
                    </button>
                </div>
                @error('password')
                    <p class="font-label-mono text-label-mono text-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label class="block font-label-mono text-label-mono text-on-surface-variant" for="password_confirmation">
                    Konfirmasi Password Baru
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">
                        lock
                    </span>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                           placeholder="Ulangi password baru"
                           class="w-full pl-10 pr-10 py-3 bg-surface-variant border-0 border-b border-outline-variant focus:border-primary focus:ring-0 focus:outline-none rounded-t-lg transition-colors font-data-table text-data-table text-on-surface">
                    <button type="button"
                            onclick="togglePassword('password_confirmation', 'visibilityIconConfirm')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface transition-colors"
                            aria-label="Tampilkan/sembunyikan password">
                        <span class="material-symbols-outlined" id="visibilityIconConfirm">visibility</span>
                    </button>
                </div>
            </div>

            <button type="submit"
                    class="w-full py-3 bg-primary text-on-primary font-title-lg text-title-lg rounded-lg shadow-sm hover:shadow-md hover:bg-primary/90 active:bg-primary/100 transition-all duration-200 flex items-center justify-center gap-2 mt-8">
                <span>Reset Password</span>
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">
                    send
                </span>
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="font-label-mono text-label-mono text-primary hover:text-secondary transition-colors inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Kembali ke Login
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

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