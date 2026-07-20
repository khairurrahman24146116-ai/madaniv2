@extends('layouts.app')

@section('content')
<x-page-header title="Edit Bobot Nilai" subtitle="{{ $scoreComponent->name }} - {{ $scoreComponent->subject->name }}" icon="tune"
    :actions="[['label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('admin.score-components.index')]]" />

<x-card variant="default" padding="lg" class="max-w-2xl">
    <form method="POST" action="{{ route('admin.score-components.update', $scoreComponent) }}">
        @csrf @method('PUT')
        <div class="space-y-5">
            <div>
                <label class="block text-label-md text-on-surface-variant mb-xs">MATA PELAJARAN</label>
                <select name="subject_id" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih mapel</option>
                    @foreach($subjects as $s)
                    <option value="{{ $s->id }}" @selected(old('subject_id', $scoreComponent->subject_id)==$s->id)>{{ $s->name }} ({{ $s->code }})</option>
                    @endforeach
                </select>
                @error('subject_id')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-xs">KOMPONEN</label>
                <select name="code" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih komponen</option>
                    <option value="tugas" @selected(old('code', $scoreComponent->code)=='tugas')>Tugas</option>
                    <option value="ph" @selected(old('code', $scoreComponent->code)=='ph')>Penilaian Harian (PH)</option>
                    <option value="uts" @selected(old('code', $scoreComponent->code)=='uts')>UTS</option>
                    <option value="uas" @selected(old('code', $scoreComponent->code)=='uas')>UAS</option>
                </select>
                @error('code')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-xs">NAMA KOMPONEN</label>
                    <input type="text" name="name" value="{{ old('name', $scoreComponent->name) }}" required
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('name')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-xs">BOBOT (%)</label>
                    <input type="number" name="weight" value="{{ old('weight', $scoreComponent->weight) }}" required step="0.01" min="0" max="100"
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('weight')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-xs">SEMESTER</label>
                    <select name="semester" required
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                        <option value="">Pilih semester</option>
                        <option value="ganjil" @selected(old('semester', $scoreComponent->semester)=='ganjil')>Ganjil</option>
                        <option value="genap" @selected(old('semester', $scoreComponent->semester)=='genap')>Genap</option>
                    </select>
                    @error('semester')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-xs">TAHUN AJARAN</label>
                    <input type="text" name="academic_year" value="{{ old('academic_year', $scoreComponent->academic_year) }}" required
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('academic_year')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex gap-md pt-md border-t border-outline-variant">
                <x-button variant="primary" type="submit" icon="save">Simpan</x-button>
                <x-button variant="outline" href="{{ route('admin.score-components.index') }}">Batal</x-button>
            </div>
        </div>
    </form>
</x-card>
@endsection
