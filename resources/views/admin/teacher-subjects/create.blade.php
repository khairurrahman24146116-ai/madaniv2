@extends('layouts.app')

@section('content')
<x-page-header title="Tambah Mapping" subtitle="Petakan guru ke mapel & kelas" icon="assignment_ind"
    :actions="[['label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('admin.teacher-subjects.index')]]" />

<x-card variant="default" padding="lg" class="max-w-2xl">
    <form method="POST" action="{{ route('admin.teacher-subjects.store') }}">
        @csrf
        <div class="space-y-5">
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">GURU</label>
                <select name="user_id" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih guru</option>
                    @foreach($teachers as $t)
                    <option value="{{ $t->id }}" @selected(old('user_id')==$t->id)>{{ $t->name }} ({{ $t->email }})</option>
                    @endforeach
                </select>
                @error('user_id')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">MATA PELAJARAN</label>
                <select name="subject_id" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih mapel</option>
                    @foreach($subjects as $s)
                    <option value="{{ $s->id }}" @selected(old('subject_id')==$s->id)>{{ $s->name }} ({{ $s->code }})</option>
                    @endforeach
                </select>
                @error('subject_id')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">KELAS</label>
                <select name="classroom_id" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih kelas</option>
                    @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" @selected(old('classroom_id')==$c->id)>{{ $c->grade }} - {{ $c->name }} ({{ $c->academic_year }})</option>
                    @endforeach
                </select>
                @error('classroom_id')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-4 pt-4 border-t border-outline-variant">
                <x-button variant="primary" type="submit" icon="save">Simpan</x-button>
                <x-button variant="outline" href="{{ route('admin.teacher-subjects.index') }}">Batal</x-button>
            </div>
        </div>
    </form>
</x-card>
@endsection
