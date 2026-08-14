@extends('layouts.guest')

@section('content')
<div class="bg-surface-container-lowest rounded-xl shadow-lg border border-surface-container-high overflow-hidden">
    {{-- Header Section --}}
    <div class="bg-primary-container p-8 flex flex-col items-center justify-center text-center rounded-t-xl">
        <div class="w-16 h-16 bg-surface-container-lowest rounded-full flex items-center justify-center mb-4 shadow-sm">
            <span class="material-symbols-outlined text-[32px] text-primary" style="font-variation-settings: 'FILL' 1;">lock_reset</span>
        </div>
        <h1 class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-on-primary-container font-bold">
            Lupa Password
        </h1>
        <p class="font-title-lg text-title-lg text-primary-fixed-dim opacity-90 mt-1">
            Masukkan email untuk menerima tautan reset
        </p>
    </div>

    {{-- Form Section --}}
    <div class="p-8">
        @if (session('status'))
            <div class="mb-6 p-4 bg-tertiary-container text-on-tertiary-container rounded-lg text-body-md flex items-start gap-3 border border-tertiary/30" role="status">
                <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                <div>{{ session('status') }}</div>
            </div>
        @endif

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

        <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
            @csrf

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

            <button type="submit"
                    class="w-full py-3 bg-primary text-on-primary font-title-lg text-title-lg rounded-lg shadow-sm hover:shadow-md hover:bg-primary/90 active:bg-primary/100 transition-all duration-200 flex items-center justify-center gap-2 mt-8">
                <span>Kirim Tautan Reset</span>
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