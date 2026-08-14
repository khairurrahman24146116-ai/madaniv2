@extends('layouts.app')

@section('content')
<x-page-header title="Tambah Kelas" subtitle="Buat kelas baru" icon="meeting_room"
    :actions="[['label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('admin.classrooms.index')]]" />

<x-card variant="default" padding="lg" class="max-w-2xl">
    <form method="POST" action="{{ route('admin.classrooms.store') }}">
        @csrf
        <div class="space-y-5">
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">NAMA KELAS</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                    placeholder="X IPA 1">
                @error('name')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">TINGKAT</label>
                <select name="grade" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih tingkat</option>
                    <option value="X" @selected(old('grade')=='X')>X (Kelas 1)</option>
                    <option value="XI" @selected(old('grade')=='XI')>XI (Kelas 2)</option>
                    <option value="XII" @selected(old('grade')=='XII')>XII (Kelas 3)</option>
                </select>
                @error('grade')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">TAHUN AJARAN</label>
                <input type="text" name="academic_year" value="{{ old('academic_year', date('Y').'/'.(date('Y')+1)) }}" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                    placeholder="2025/2026">
                @error('academic_year')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">WALI KELAS</label>
                <select name="wali_kelas_id"
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih wali kelas</option>
                    @foreach($gurus ?? [] as $g)
                    <option value="{{ $g->id }}" @selected(old('wali_kelas_id')==$g->id)>{{ $g->name }}</option>
                    @endforeach
                </select>
                @error('wali_kelas_id')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">DESKRIPSI</label>
                <textarea name="description" rows="3"
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">{{ old('description') }}</textarea>
            </div>
            <div class="flex gap-4 pt-4 border-t border-outline-variant">
                <x-button variant="primary" type="submit" icon="save">Simpan</x-button>
                <x-button variant="outline" href="{{ route('admin.classrooms.index') }}">Batal</x-button>
            </div>
        </div>
    </form>
</x-card>
@endsection
