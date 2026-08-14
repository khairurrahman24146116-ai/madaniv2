@extends('layouts.app')

@section('content')
<x-page-header title="Tambah Mapel" subtitle="Buat mata pelajaran baru" icon="book"
    :actions="[['label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('admin.subjects.index')]]" />

<x-card variant="default" padding="lg" class="max-w-2xl">
    <form method="POST" action="{{ route('admin.subjects.store') }}">
        @csrf
        <div class="space-y-5">
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">NAMA MAPEL</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                    placeholder="Fisika">
                @error('name')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">KODE MAPEL</label>
                <input type="text" name="code" value="{{ old('code') }}" required maxlength="10"
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                    placeholder="FIS">
                @error('code')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">DESKRIPSI</label>
                <textarea name="description" rows="3"
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">{{ old('description') }}</textarea>
            </div>
            <div class="flex gap-4 pt-4 border-t border-outline-variant">
                <x-button variant="primary" type="submit" icon="save">Simpan</x-button>
                <x-button variant="outline" href="{{ route('admin.subjects.index') }}">Batal</x-button>
            </div>
        </div>
    </form>
</x-card>
@endsection
