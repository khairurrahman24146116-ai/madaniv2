@extends('layouts.app')

@section('content')
<x-page-header title="Edit Kelas" subtitle="{{ $classroom->name }}" icon="meeting_room"
    :actions="[['label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('admin.classrooms.index')]]" />

<x-card variant="default" padding="lg" class="max-w-2xl">
    <form method="POST" action="{{ route('admin.classrooms.update', $classroom) }}">
        @csrf @method('PUT')
        <div class="space-y-5">
            <div>
                <label class="block text-label-md text-on-surface-variant mb-xs">NAMA KELAS</label>
                <input type="text" name="name" value="{{ old('name', $classroom->name) }}" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('name')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-xs">TINGKAT</label>
                <select name="grade" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="X" @selected(old('grade', $classroom->grade)=='X')>X (Kelas 1)</option>
                    <option value="XI" @selected(old('grade', $classroom->grade)=='XI')>XI (Kelas 2)</option>
                    <option value="XII" @selected(old('grade', $classroom->grade)=='XII')>XII (Kelas 3)</option>
                </select>
                @error('grade')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-xs">TAHUN AJARAN</label>
                <input type="text" name="academic_year" value="{{ old('academic_year', $classroom->academic_year) }}" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('academic_year')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-xs">DESKRIPSI</label>
                <textarea name="description" rows="3"
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">{{ old('description', $classroom->description) }}</textarea>
            </div>
            <div class="flex gap-md pt-md border-t border-outline-variant">
                <x-button variant="primary" type="submit" icon="save">Simpan</x-button>
                <x-button variant="outline" href="{{ route('admin.classrooms.index') }}">Batal</x-button>
            </div>
        </div>
    </form>
</x-card>
@endsection
