@extends('layouts.guest')

@section('content')
<div class="bg-surface-container-lowest rounded-xl shadow-lg border border-surface-container-high overflow-hidden">
    {{-- Header Section --}}
    <div class="bg-white p-8 flex flex-col items-center justify-center text-center rounded-t-xl">
        <x-app-logo size="lg" class="mb-4" />
        <h1 class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-primary font-bold">
            SIAKAD
        </h1>
        <p class="font-title-lg text-title-lg text-on-surface-variant opacity-90 mt-1">
            SMA Madani Al-Aziziyah
        </p>
    </div>

    {{-- Form Section --}}
    <div class="p-8">
        @if(session('errors'))
            <div class="mb-6 p-4 bg-error-container text-on-error-container rounded-lg text-body-md flex items-start gap-3 border border-error/30" role="alert">
                <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5">error</span>
                <div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach(session('errors')->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('auth.login.web') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Email Field --}}
            <div class="space-y-2">
                <label class="block font-label-mono text-label-mono text-on-surface-variant" for="email">
                    Username / ID Siswa / NIP
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">
                        mail
                    </span>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                           placeholder="Masukkan ID Anda"
                           class="w-full pl-10 pr-4 py-3 bg-surface-variant border-0 border-b border-outline-variant focus:border-primary focus:ring-0 focus:outline-none rounded-t-lg transition-colors font-data-table text-data-table text-on-surface
                                  @error('email') border-error @enderror">
                </div>
                @error('email')
                    <p class="font-label-mono text-label-mono text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Field --}}
            <div class="space-y-2">
                <label class="block font-label-mono text-label-mono text-on-surface-variant" for="password">
                    Password
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">
                        lock
                    </span>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full pl-10 pr-10 py-3 bg-surface-variant border-0 border-b border-outline-variant focus:border-primary focus:ring-0 focus:outline-none rounded-t-lg transition-colors font-data-table text-data-table text-on-surface
                                  @error('password') border-error @enderror">
                    <button type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface transition-colors"
                            aria-label="Tampilkan/sembunyikan password">
                        <span class="material-symbols-outlined" id="visibilityIcon">visibility</span>
                    </button>
                </div>
                <div class="flex justify-end gap-4 mt-1">
                    <label class="flex items-center gap-2 cursor-pointer group mr-auto">
                        <input class="w-4 h-4 rounded border-outline-variant accent-primary focus:ring-2 focus:ring-primary/25"
                               type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} />
                        <span class="font-label-mono text-label-mono text-on-surface-variant group-hover:text-on-surface transition-colors">Ingat Saya</span>
                    </label>
                    <a class="font-label-mono text-label-mono text-primary hover:text-secondary transition-colors"
                       href="{{ route('password.request') }}">Lupa Password?</a>
                </div>
                @error('password')
                    <p class="font-label-mono text-label-mono text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                    class="w-full py-3 bg-primary text-on-primary font-title-lg text-title-lg rounded-lg shadow-sm hover:shadow-md hover:bg-primary/90 active:bg-primary/100 transition-all duration-200 flex items-center justify-center gap-2 mt-8">
                <span>Masuk</span>
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">
                    login
                </span>
            </button>
        </form>
    </div>

    {{-- Footer --}}
    <div class="bg-surface-container-low py-4 px-8 text-center border-t border-surface-container-high rounded-b-xl">
        <p class="font-label-mono text-label-mono text-outline">
            Sistem Informasi Manajemen Sekolah v2.4.1 © 2026
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const visibilityIcon = document.getElementById('visibilityIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            visibilityIcon.textContent = 'visibility_off';
        } else {
            passwordInput.type = 'password';
            visibilityIcon.textContent = 'visibility';
        }
    }
</script>
@endpush