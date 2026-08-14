@extends('layouts.app')

@section('content')
<x-page-header title="Tambah Jadwal" subtitle="Buat jadwal mengajar baru" icon="calendar_month"
    :actions="[['label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('admin.schedules.index')]]" />

<x-card variant="default" padding="lg" class="max-w-2xl">
    <form method="POST" action="{{ route('admin.schedules.store') }}">
        @csrf
        <div class="space-y-5">
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">MAPPING GURU-MAPEL-KELAS</label>
                <select name="teacher_subject_id" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih mapping</option>
                    @foreach($mappings as $m)
                    <option value="{{ $m->id }}" @selected(old('teacher_subject_id')==$m->id)>
                        {{ $m->user->name }} - {{ $m->subject->name }} ({{ $m->classroom->name }})
                    </option>
                    @endforeach
                </select>
                @error('teacher_subject_id')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">HARI</label>
                <select name="day" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih hari</option>
                    @foreach(['senin','selasa','rabu','kamis','jumat','sabtu'] as $day)
                    <option value="{{ $day }}" @selected(old('day')==$day)>{{ ucfirst($day) }}</option>
                    @endforeach
                </select>
                @error('day')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-1">JAM MULAI</label>
                    <input type="time" name="start_time" value="{{ old('start_time', '14:00') }}" min="14:00" max="16:00" required
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('start_time')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-1">JAM SELESAI</label>
                    <input type="time" name="end_time" value="{{ old('end_time', '14:50') }}" min="14:00" max="16:00" required
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('end_time')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">JAM KE-</label>
                <select name="hour_order" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih urutan jam</option>
                    @for($i = 1; $i <= 4; $i++)
                    <option value="{{ $i }}" @selected(old('hour_order')==$i)>{{ $i }}</option>
                    @endfor
                </select>
                @error('hour_order')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-4 pt-4 border-t border-outline-variant">
                <x-button variant="primary" type="submit" icon="save">Simpan</x-button>
                <x-button variant="outline" href="{{ route('admin.schedules.index') }}">Batal</x-button>
            </div>
        </div>
    </form>
</x-card>
@endsection
