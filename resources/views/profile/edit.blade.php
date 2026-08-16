@extends('layouts.app')

@section('content')
<x-page-header 
    title="Profil Saya" 
    subtitle="Edit informasi profil dan foto"
    icon="person"
/>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Photo Card --}}
    <x-card variant="default" padding="lg" class="lg:col-span-1">
        <div class="flex flex-col items-center text-center">
            <div class="relative mb-4">
                @if(auth()->user()->profile_photo_url)
                    <img src="{{ auth()->user()->profile_photo_url }}" 
                         alt="{{ auth()->user()->name }}"
                         class="w-32 h-32 rounded-full object-cover border-4 border-tertiary-fixed/60 shadow-md">
                @else
                    <div class="w-32 h-32 rounded-full bg-tertiary-fixed/40 flex items-center justify-center border-4 border-tertiary-fixed/60 shadow-md">
                        <span class="text-4xl font-bold text-tertiary-container">{{ substr(auth()->user()->name, 0, 1) }}</span>
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
            <div class="space-y-4">
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

                @if($ttdRoleLabel)
                <div class="pt-4 border-t border-outline-variant">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-primary">draw</span>
                        <h3 class="text-title-lg font-bold text-on-surface">Tanda Tangan Digital</h3>
                    </div>
                    <p class="text-body-md text-on-surface-variant mb-4">
                        Tanda tangan {{ $ttdRoleLabel }} dipakai di atas nama pada PDF rapor dan surat resmi. Format: PNG transparan, maksimal 2MB.
                    </p>

                    @if($ttdAktif)
                    <div class="flex items-center gap-4 p-4 rounded-lg bg-surface-container-low border border-outline-variant mb-4">
                        <img src="{{ asset('storage/'.$ttdAktif->file_path) }}" alt="Tanda tangan aktif" class="h-16 bg-white rounded border border-outline-variant p-1">
                        <div>
                            <p class="text-body-md font-semibold text-on-surface">Tanda tangan aktif</p>
                            <p class="text-caption text-on-surface-variant">Diunggah {{ $ttdAktif->created_at->locale('id')->isoFormat('D MMMM Y') }}. Unggah baru untuk mengganti.</p>
                        </div>
                    </div>
                    @else
                    <div class="p-4 rounded-lg bg-warning-container/40 border border-warning-container mb-4">
                        <p class="text-body-md text-on-warning-container">Belum ada tanda tangan. Unggah PNG transparan di bawah ini.</p>
                    </div>
                    @endif

                    <x-form-input type="file" name="tanda_tangan" label="Unggah Tanda Tangan (PNG transparan)" hint="Posisi tanda tangan otomatis ditempel di atas nama." :error="$errors->first('tanda_tangan')" />

                    @if($ttdAktif)
                    <div class="flex items-center gap-2 mt-3">
                        <input type="checkbox" name="hapus_tanda_tangan" id="hapus_tanda_tangan" value="1" class="w-4 h-4 rounded border-outline-variant text-error focus:ring-2 focus:ring-error/20">
                        <label for="hapus_tanda_tangan" class="text-body-md text-error cursor-pointer">Hapus tanda tangan saat ini</label>
                    </div>
                    @endif
                </div>
                @endif

                <div class="flex gap-4 pt-4 border-t border-outline-variant">
                    <x-button variant="primary" type="submit" icon="save">Simpan Perubahan</x-button>
                </div>
            </div>
        </form>
    </x-card>
</div>
@endsection
