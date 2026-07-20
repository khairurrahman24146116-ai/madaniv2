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
<x-card variant="default" padding="md" class="mb-lg">
    <div class="flex justify-between items-start mb-sm">
        <div>
            <h2 class="text-headline-md text-on-surface">{{ $schedule->teacherSubject->subject->name ?? 'Fisika' }} - {{ $schedule->teacherSubject->classroom->name ?? 'Kelas XI' }}</h2>
            <p class="text-body-md text-on-surface-variant">{{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
        <div class="flex items-center gap-sm">
            <div class="bg-secondary-container text-on-secondary-container px-sm py-xs rounded-lg text-label-md">
                {{ $schedule->start_time ?? '14:00' }} - {{ $schedule->end_time ?? '16:00' }}
            </div>
            <div class="bg-surface-container text-on-surface-variant px-sm py-xs rounded-lg text-label-md tabular-nums" id="current-time-header">
                {{ now()->format('H:i:s') }}
            </div>
        </div>
    </div>
    <div class="flex flex-wrap gap-sm mt-md" id="summary-chips">
        <div class="flex items-center gap-xs px-md py-xs bg-surface-container rounded-full border border-outline-variant">
            <span class="text-label-md text-on-surface-variant uppercase">Total:</span>
            <span class="text-headline-md text-on-surface" id="count-total">{{ count($students) }}</span>
        </div>
        <div class="flex items-center gap-xs px-md py-xs bg-green-50 text-green-700 rounded-full border border-green-200 dark:bg-green-900/20 dark:text-green-300 dark:border-green-800">
            <span class="text-label-md uppercase">Hadir:</span>
            <span class="text-headline-md" id="count-present">{{ count($students) }}</span>
        </div>
        <div class="flex items-center gap-xs px-md py-xs bg-amber-50 text-amber-700 rounded-full border border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-800">
            <span class="text-label-md uppercase">Izin/Sakit:</span>
            <span class="text-headline-md" id="count-excused">0</span>
        </div>
        <div class="flex items-center gap-xs px-md py-xs bg-red-50 text-red-700 rounded-full border border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-800">
            <span class="text-label-md uppercase">Alpa:</span>
            <span class="text-headline-md" id="count-absent">0</span>
        </div>
    </div>
</x-card>

{{-- Schedule Selector --}}
<form action="{{ route('attendances.realtime') }}" method="GET" class="flex flex-wrap gap-md mb-lg">
    <div>
        <label class="text-label-md text-on-surface-variant block mb-xs">JADWAL</label>
        <select name="schedule_id" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md" onchange="this.form.submit()">
            @foreach($schedules ?? [] as $s)
            <option value="{{ $s->id }}" @selected(($schedule->id ?? null) == $s->id)>{{ $s->teacherSubject->subject->name }} - {{ $s->teacherSubject->classroom->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-label-md text-on-surface-variant block mb-xs">TANGGAL</label>
        <input type="date" name="date" value="{{ $date }}" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md">
    </div>
    <div class="self-end">
        <x-button variant="primary" type="submit" icon="visibility">Tampilkan</x-button>
    </div>
</form>

{{-- Ensure Tailwind v4 generates status color classes --}}
<span class="hidden bg-amber-500 bg-blue-500 bg-red-600 text-white"></span>

{{-- Student List --}}
<form action="{{ route('attendances.store') }}" method="POST">
    @csrf
    <input type="hidden" name="schedule_id" value="{{ $schedule->id ?? '' }}">
    <input type="hidden" name="date" value="{{ $date }}">

    <div class="space-y-sm" id="student-list">
        @forelse($students as $student)
        <div class="student-card x-card p-sm flex flex-col sm:flex-row items-center gap-md transition-colors" data-student-id="{{ $student->id }}">
            <div class="flex items-center gap-md w-full sm:w-auto">
                <div class="w-12 h-12 rounded-full overflow-hidden bg-surface-container flex-shrink-0 flex items-center justify-center font-bold text-on-surface-variant">
                    {{ strtoupper(substr($student->name, 0, 2)) }}
                </div>
                <div class="flex-grow">
                    <p class="text-headline-md text-on-surface leading-tight">{{ $student->name }}</p>
                    <p class="text-caption text-on-surface-variant">NIS: {{ $student->nis }}</p>
                </div>
            </div>
            <div class="flex w-full sm:w-auto bg-surface-container p-xs rounded-lg gap-xs">
                <input type="hidden" name="attendances[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                <input type="hidden" name="attendances[{{ $loop->index }}][status]" value="H" class="attendance-status">
                @foreach(['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa'] as $key => $label)
                <button type="button" onclick="setStatus(this, '{{ $key }}')" class="status-btn flex-1 sm:w-14 py-1 px-2 rounded-md text-label-md text-center text-on-surface-variant transition-all hover:bg-surface-container-high active:scale-95 @if($key === 'H') bg-green-600 text-white @endif" data-status="{{ $key }}" @if($key === 'H') data-selected="H" @endif>{{ $key }}</button>
                @endforeach
            </div>
        </div>
        @empty
        <div class="text-center py-xl text-on-surface-variant">
            <span class="material-symbols-outlined text-4xl mb-md">people</span>
            <p>Pilih jadwal dan tanggal untuk menampilkan daftar siswa.</p>
        </div>
        @endforelse
    </div>

    @if(count($students) > 0)
    @if(!$canEdit)
    <div class="mb-lg p-md bg-amber-50 text-amber-800 rounded-xl text-[14px] flex items-start gap-3 border border-amber-200">
        <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">schedule</span>
        <div>
            <p class="font-semibold">Di luar jam operasional</p>
            <p class="mt-1">Absensi hanya dapat diisi dalam rentang blok sore (14:00 - 16:00 WIB).</p>
        </div>
    </div>
    @endif
    <div class="fixed bottom-16 left-0 right-0 bg-surface border-t border-outline-variant p-md z-40 max-w-7xl mx-auto shadow-lg md:rounded-t-2xl flex flex-col md:flex-row items-center justify-between gap-md">
        <div class="text-center md:text-left">
            <p class="text-caption text-on-surface-variant">Konfirmasi Absensi</p>
            <p class="text-label-md text-on-surface font-semibold" id="current-time">Timestamp: {{ now()->format('H:i:s') }}</p>
        </div>
        <x-button type="submit" variant="primary" size="xl" icon="send" icon-position="right" class="w-full md:w-auto px-xl py-md" :disabled="!$canEdit">
            Submit Absensi
        </x-button>
    </div>
    @endif
</form>
@endsection

@push('scripts')
<script>
    function setStatus(btn, status) {
        const parent = btn.closest('.student-card');
        const prevSelected = parent.querySelector('[data-selected]');

        if (prevSelected && prevSelected.getAttribute('data-selected') === status) {
            return;
        }

        if (prevSelected) {
            prevSelected.removeAttribute('data-selected');
            prevSelected.classList.remove('bg-green-600', 'bg-amber-500', 'bg-blue-500', 'bg-red-600', 'text-white');
            prevSelected.classList.add('text-on-surface-variant');
        }

        btn.classList.remove('text-on-surface-variant');
        btn.classList.remove('bg-green-600', 'bg-amber-500', 'bg-blue-500', 'bg-red-600', 'text-white');
        const colors = { H: 'bg-green-600', S: 'bg-amber-500', I: 'bg-blue-500', A: 'bg-red-600' };
        btn.classList.add(colors[status], 'text-white');
        btn.setAttribute('data-selected', status);

        parent.querySelector('.attendance-status').value = status;

        updateCounts();
    }

    function updateCounts() {
        let counts = { H: 0, S: 0, I: 0, A: 0 };
        document.querySelectorAll('[data-selected]').forEach(function (btn) {
            var s = btn.getAttribute('data-selected');
            if (counts[s] !== undefined) counts[s]++;
        });
        document.getElementById('count-present').textContent = counts.H;
        document.getElementById('count-excused').textContent = counts.S + counts.I;
        document.getElementById('count-absent').textContent = counts.A;
    }

    function updateTime() {
        const t = new Date().toLocaleTimeString('id-ID');
        const el = document.getElementById('current-time');
        if (el) el.textContent = 'Timestamp: ' + t;
        const header = document.getElementById('current-time-header');
        if (header) header.textContent = t;
    }
    setInterval(updateTime, 1000);
</script>
@endpush