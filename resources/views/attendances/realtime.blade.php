@extends('layouts.app')

@section('content')
@php
$schedule = $schedule ?? null;
$students = $students ?? [];
$date = $date ?? now()->format('Y-m-d');
$canEdit = $canEdit ?? true;
@endphp

<x-page-header 
    title="Absensi Real-Time" 
    subtitle="Absensi cepat per jam pelajaran"
    icon="speed"
    :actions="[
        ['type' => 'button', 'label' => 'Form Absensi', 'icon' => 'how_to_reg', 'variant' => 'secondary', 'href' => route('attendances.form')],
        ['type' => 'button', 'label' => 'Riwayat', 'icon' => 'history', 'variant' => 'outline', 'href' => route('attendances.index')],
    ]"
/>

{{-- Info Bar --}}
<x-card variant="default" padding="md" class="mb-6">
    <div class="flex flex-wrap gap-x-3 gap-y-2 items-start justify-between mb-2">
        <div class="min-w-0">
            <h2 class="text-headline-md text-on-surface">{{ $schedule->teacherSubject->subject->name ?? 'Fisika' }} - {{ $schedule->teacherSubject->classroom->name ?? 'Kelas XI' }}</h2>
            <p class="text-body-md text-on-surface-variant">{{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <div class="bg-secondary-container text-on-secondary-container px-2 py-1 rounded-lg text-label-md">
                {{ $schedule->start_time ?? '14:00' }} - {{ $schedule->end_time ?? '16:00' }}
            </div>
            <div class="bg-surface-container text-on-surface-variant px-2 py-1 rounded-lg text-label-md tabular-nums" id="current-time-header">
                {{ now()->format('H:i:s') }}
            </div>
        </div>
    </div>
    <div class="flex flex-wrap gap-2 mt-4" id="summary-chips">
        <div class="flex items-center gap-1 px-4 py-1 bg-surface-container rounded-full border border-outline-variant">
            <span class="text-label-md text-on-surface-variant uppercase">Total:</span>
            <span class="text-headline-md text-on-surface" id="count-total">{{ count($students) }}</span>
        </div>
        <div class="flex items-center gap-1 px-4 py-1 bg-tertiary-fixed/40 text-tertiary-container rounded-full border border-tertiary/30   ">
            <span class="text-label-md uppercase">Hadir:</span>
            <span class="text-headline-md" id="count-present">{{ count($students) }}</span>
        </div>
        <div class="flex items-center gap-1 px-4 py-1 bg-warning-container/50 text-warning rounded-full border border-warning/30   ">
            <span class="text-label-md uppercase">Izin/Sakit:</span>
            <span class="text-headline-md" id="count-excused">0</span>
        </div>
        <div class="flex items-center gap-1 px-4 py-1 bg-error-container/50 text-error rounded-full border border-error/30   ">
            <span class="text-label-md uppercase">Alpa:</span>
            <span class="text-headline-md" id="count-absent">0</span>
        </div>
    </div>
</x-card>

{{-- Schedule Selector --}}
<form action="{{ route('attendances.realtime') }}" method="GET" class="flex flex-wrap gap-4 mb-6">
    <div>
        <label class="text-label-md text-on-surface-variant block mb-1">JADWAL</label>
        <select name="schedule_id" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md" onchange="this.form.submit()">
            @foreach($schedules ?? [] as $s)
            <option value="{{ $s->id }}" @selected(($schedule->id ?? null) == $s->id)>{{ $s->teacherSubject->subject->name }} - {{ $s->teacherSubject->classroom->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-label-md text-on-surface-variant block mb-1">TANGGAL</label>
        <input type="date" name="date" value="{{ $date }}" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md">
    </div>
    <div class="self-end">
        <x-button variant="primary" type="submit" icon="visibility">Tampilkan</x-button>
    </div>
</form>

{{-- Student List --}}
<form action="{{ route('attendances.store') }}" method="POST">
    @csrf
    <input type="hidden" name="schedule_id" value="{{ $schedule->id ?? '' }}">
    <input type="hidden" name="date" value="{{ $date }}">

    <div class="space-y-2" id="student-list">
        @forelse($students as $student)
        <div class="student-card x-card p-2 flex flex-col sm:flex-row items-center gap-4 transition-colors" data-student-id="{{ $student->id }}">
            <div class="flex items-center gap-4 w-full sm:w-auto">
                <div class="w-12 h-12 rounded-full overflow-hidden bg-surface-container flex-shrink-0 flex items-center justify-center font-bold text-on-surface-variant">
                    {{ strtoupper(substr($student->name, 0, 2)) }}
                </div>
                <div class="flex-grow">
                    <p class="text-headline-md text-on-surface leading-tight">{{ $student->name }}</p>
                    <p class="text-caption text-on-surface-variant">NIS: {{ $student->nis }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 w-full sm:w-auto">
                <input type="hidden" name="attendances[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                <select name="attendances[{{ $loop->index }}][status]" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-full sm:w-32" onchange="updateCounts()">
                    <option value="H">Hadir</option>
                    <option value="S">Sakit</option>
                    <option value="I">Izin</option>
                    <option value="A">Alpa</option>
                </select>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-on-surface-variant">
            <span class="material-symbols-outlined text-4xl mb-4">people</span>
            <p>Pilih jadwal dan tanggal untuk menampilkan daftar siswa.</p>
        </div>
        @endforelse
    </div>

    @if(count($students) > 0)
    @if(!$canEdit)
    <div class="mb-6 p-4 bg-warning-container/50 text-on-warning-container rounded-xl text-[14px] flex items-start gap-3 border border-warning/30">
        <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">schedule</span>
        <div>
            <p class="font-semibold">Di luar jam operasional</p>
            <p class="mt-1">Absensi hanya dapat diisi dalam rentang blok sore (14:00 - 16:00 WIB).</p>
        </div>
    </div>
    @endif
    <div class="mt-6">
        <x-button type="submit" variant="primary" size="xl" icon="send" icon-position="right" class="w-full md:w-auto px-8 py-4" :disabled="!$canEdit">
            Submit Absensi
        </x-button>
    </div>
    @endif
</form>
@endsection

@push('scripts')
<script>
    function updateCounts() {
        const selects = document.querySelectorAll('.student-card select');
        let counts = { H: 0, S: 0, I: 0, A: 0 };
        selects.forEach(s => { counts[s.value]++; });
        document.getElementById('count-present').textContent = counts.H;
        document.getElementById('count-excused').textContent = counts.S + counts.I;
        document.getElementById('count-absent').textContent = counts.A;
    }

    function updateTime() {
        const t = new Date().toLocaleTimeString('id-ID');
        const header = document.getElementById('current-time-header');
        if (header) header.textContent = t;
    }

    document.querySelectorAll('.student-card select').forEach(s => {
        s.addEventListener('change', updateCounts);
    });

    updateCounts();
    setInterval(updateTime, 1000);
</script>
@endpush