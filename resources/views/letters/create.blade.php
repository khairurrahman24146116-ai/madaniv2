@extends('layouts.app')

@section('content')
<x-page-header 
    title="Buat Surat" 
    subtitle="Buat surat atau pengumuman sekolah"
    icon="add"
    :actions="[
        ['type' => 'button', 'label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('admin.letters.index')],
    ]"
/>

<form action="{{ route('admin.letters.store') }}" method="POST" class="max-w-3xl space-y-md">
    @csrf
    <x-card variant="default" padding="lg">
        <div class="space-y-md">
            <div>
                <label class="text-label-md text-on-surface-variant block mb-xs">JUDUL SURAT</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-full">
                @error('title') <p class="text-error text-label-md mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-label-md text-on-surface-variant block mb-xs">TIPE SURAT</label>
                <select name="type" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-full">
                    <option value="pengumuman">Pengumuman</option>
                    <option value="edaran">Surat Edaran</option>
                    <option value="surat_resmi">Surat Resmi</option>
                    <option value="lainnya">Lainnya</option>
                </select>
                @error('type') <p class="text-error text-label-md mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-label-md text-on-surface-variant block mb-xs">ISI SURAT</label>
                <textarea name="content" rows="12" required class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-full">{{ old('content') }}</textarea>
                @error('content') <p class="text-error text-label-md mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </x-card>
    <div class="flex gap-sm">
        <x-button variant="primary" type="submit" icon="send">Terbitkan Surat</x-button>
        <a href="{{ route('admin.letters.index') }}" class="px-lg py-2 border border-outline-variant rounded-lg text-label-md text-on-surface-variant hover:bg-surface-container transition-colors inline-flex items-center">Batal</a>
    </div>
</form>
@endsection
