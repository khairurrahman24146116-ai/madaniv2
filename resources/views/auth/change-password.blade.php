@extends('layouts.app')

@section('content')
    <x-page-header
        title="Ganti Password"
        subtitle="Anda wajib mengganti password sebelum melanjutkan"
        icon="lock_reset"
    />

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-300 rounded-xl text-[14px] flex items-start gap-3 border border-green-200 dark:border-green-800">
            <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">check_circle</span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-300 rounded-xl text-[14px] flex items-start gap-3 border border-red-200 dark:border-red-800">
            <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">error</span>
            <div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="max-w-xl">
        <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg text-sm text-on-surface dark:text-on-surface">
            Demi keamanan akun, password awal yang diberikan sekolah harus segera diganti. Anda tidak dapat mengakses halaman lain sampai password diganti.
        </div>

        <x-card variant="default" padding="lg">
            <form action="{{ route('password.change.update') }}" method="POST" class="space-y-5">
                @csrf
                <x-form-input
                    type="password"
                    name="current_password"
                    label="Password Saat Ini"
                    required
                    autocomplete="current-password"
                    :error="$errors->first('current_password')"
                />

                <x-form-input
                    type="password"
                    name="password"
                    label="Password Baru"
                    hint="Minimal 8 karakter"
                    required
                    autocomplete="new-password"
                    :error="$errors->first('password')"
                />

                <x-form-input
                    type="password"
                    name="password_confirmation"
                    label="Konfirmasi Password Baru"
                    required
                    autocomplete="new-password"
                    :error="$errors->first('password_confirmation')"
                />

                <div class="pt-4 border-t border-outline-variant">
                    <x-button variant="primary" type="submit" icon="lock_reset">
                        Simpan Password
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
