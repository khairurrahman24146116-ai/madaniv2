@extends('layouts.app')

@section('content')
<x-page-header 
    title="Pindah Kelas" 
    subtitle="{{ $student->name }} (NIS: {{ $student->nis }})"
    icon="swap_horiz"
    :actions="[
        ['type' => 'button', 'label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('admin.students.index')],
    ]"
/>

<x-card variant="default" padding="lg" class="max-w-2xl">
    <div class="mb-6 p-4 bg-secondary-fixed/40 text-on-secondary-fixed-variant rounded-xl text-[14px] flex items-start gap-3 border border-secondary/30">
        <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">info</span>
        <div>
            <p class="font-semibold">Kelas saat ini: {{ $student->classroom->name ?? '-' }}</p>
            <p class="mt-1">Pilih kelas baru untuk memindahkan siswa ini.</p>
        </div>
    </div>

    <form action="{{ route('admin.students.move', $student) }}" method="POST">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">KELAS TUJUAN</label>
                <select name="classroom_id" required class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih kelas</option>
                    @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" @selected($c->id === $student->classroom_id)>{{ $c->grade }} - {{ $c->name }} ({{ $c->academic_year }})</option>
                    @endforeach
                </select>
                @error('classroom_id')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-4 pt-4 border-t border-outline-variant">
                <x-button variant="primary" type="submit" icon="swap_horiz">Pindahkan</x-button>
                <x-button variant="outline" href="{{ route('admin.students.index') }}">Batal</x-button>
            </div>
        </div>
    </form>
</x-card>
@endsection
