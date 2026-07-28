@extends('layouts.app')

@section('content')
<x-page-header 
    title="Profil Saya" 
    subtitle="Edit informasi profil dan foto"
    icon="person"
/>

@if(session('success'))
<div class="mb-lg p-md bg-green-50 text-green-800 rounded-xl text-[14px] flex items-start gap-3 border border-green-200">
    <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">check_circle</span>
    <div>{{ session('success') }}</div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-lg">
    {{-- Photo Card --}}
    <x-card variant="default" padding="lg" class="lg:col-span-1">
        <div class="flex flex-col items-center text-center">
            <div class="relative mb-md">
                @if(auth()->user()->profile_photo_url)
                    <img src="{{ auth()->user()->profile_photo_url }}" 
                         alt="{{ auth()->user()->name }}"
                         class="w-32 h-32 rounded-full object-cover border-4 border-green-100 shadow-md">
                @else
                    <div class="w-32 h-32 rounded-full bg-green-100 flex items-center justify-center border-4 border-green-100 shadow-md">
                        <span class="text-4xl font-bold text-green-700">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                @endif
            </div>
            <h3 class="text-headline-md font-bold text-on-surface">{{ auth()->user()->name }}</h3>
            <p class="text-body-md text-on-surface-variant capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
            <p class="text-caption text-on-surface-variant mt-1">{{ auth()->user()->email }}</p>
        </div>
    </x-card>

    {{-- Form Card --}}
    <x-card variant="default" padding="lg" class="lg:col-span-2">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-md">
                <x-form-input type="text" name="name" label="Nama Lengkap" :value="auth()->user()->name" required :error="$errors->first('name')" />

                <x-form-input type="email" name="email" label="Email" :value="auth()->user()->email" required :error="$errors->first('email')" />

                <x-form-input type="tel" name="phone" label="No. Telepon" :value="auth()->user()->phone" placeholder="08xxxxxxxxxx" :error="$errors->first('phone')" />

                <x-form-input type="textarea" name="address" label="Alamat" :value="auth()->user()->address" rows="3" :error="$errors->first('address')" />

                <x-form-input type="file" name="profile_photo" label="Foto Profil" hint="Format: JPG, PNG. Maksimal 2MB." :error="$errors->first('profile_photo')" />

                @if(auth()->user()->profile_photo_path)
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remove_photo" id="remove_photo" value="1" class="w-4 h-4 rounded border-outline-variant text-error focus:ring-2 focus:ring-error/20">
                    <label for="remove_photo" class="text-body-md text-error cursor-pointer">Hapus foto profil saat ini</label>
                </div>
                @endif

                <div class="flex gap-md pt-md border-t border-outline-variant">
                    <x-button variant="primary" type="submit" icon="save">Simpan Perubahan</x-button>
                </div>
            </div>
        </form>
    </x-card>
</div>
@endsection
