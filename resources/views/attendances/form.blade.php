@extends('layouts.app')

@section('content')
@php
$schedule = $schedule ?? null;
$students = $students ?? [];
$date = $date ?? now()->format('Y-m-d');
$canEdit = $canEdit ?? true;
@endphp

<x-page-header 
    title="Form Absensi" 
    subtitle="Input kehadiran siswa per jam pelajaran"
    icon="how_to_reg"
    :actions="[
        ['type' => 'button', 'label' => 'Realtime', 'icon' => 'speed', 'variant' => 'secondary', 'href' => route('attendances.realtime')],
        ['type' => 'button', 'label' => 'Riwayat', 'icon' => 'history', 'variant' => 'outline', 'href' => route('attendances.index')],
    ]"
/>

@if(session('success'))
<div class="mb-lg p-md bg-green-50 text-green-800 rounded-xl text-[14px] flex items-start gap-3 border border-green-200">
    <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">check_circle</span>
    <div>{{ session('success') }}</div>
</div>
@endif

@if(session('error'))
<div class="mb-lg p-md bg-red-50 text-red-800 rounded-xl text-[14px] flex items-start gap-3 border border-red-200">
    <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">error</span>
    <div>{{ session('error') }}</div>
</div>
@endif

{{-- Info Section --}}
<x-card variant="default" padding="md" class="mb-lg">
    <div class="flex justify-between items-start mb-sm">
        <div>
            <h2 class="text-headline-md text-on-surface">{{ $schedule->teacherSubject->subject->name ?? 'Pilih Jadwal' }} - {{ $schedule->teacherSubject->classroom->name ?? '' }}</h2>
            <p class="text-body-md text-on-surface-variant">{{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
        <div class="bg-secondary-container text-on-secondary-container px-sm py-xs rounded-lg text-label-md">
            {{ $schedule->start_time ?? '14:00' }} - {{ $schedule->end_time ?? '14:50' }}
        </div>
    </div>
    <div class="flex flex-wrap gap-sm mt-md" id="summary-chips">
        <div class="flex items-center gap-xs px-md py-xs bg-surface-container rounded-full border border-outline-variant">
            <span class="text-label-md text-on-surface-variant uppercase">Total:</span>
            <span class="text-headline-md text-on-surface" id="count-total">{{ count($students) }}</span>
        </div>
        <div class="flex items-center gap-xs px-md py-xs bg-green-50 text-green-700 rounded-full border border-green-200 dark:bg-green-900/20 dark:text-green-300 dark:border-green-800">
            <span class="text-label-md uppercase">Hadir:</span>
            <span class="text-headline-md" id="count-present">0</span>
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

{{-- Schedule Selection --}}
<form action="{{ route('attendances.form') }}" method="GET" class="flex flex-wrap gap-md mb-lg">
    <div>
        <label class="text-label-md text-on-surface-variant block mb-xs">JADWAL</label>
        <select name="schedule_id" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface focus:ring-primary focus:border-primary py-2 px-3 text-body-md" onchange="this.form.submit()">
            @foreach($schedules ?? [] as $sched)
            <option value="{{ $sched->id }}" @selected(($schedule->id ?? null) == $sched->id)>
                {{ $sched->teacherSubject->subject->name }} - {{ $sched->teacherSubject->classroom->name }} ({{ $sched->day }}, {{ $sched->start_time }}-{{ $sched->end_time }})
            </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-label-md text-on-surface-variant block mb-xs">TANGGAL</label>
        <input type="date" name="date" value="{{ $date }}" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface focus:ring-primary focus:border-primary py-2 px-3 text-body-md">
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

    <div class="space-y-sm">
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
                @foreach(['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa'] as $key => $label)
                <label class="status-choice flex-1 sm:w-14 py-2 px-2 rounded-md text-label-md text-center text-on-surface-variant transition-colors hover:bg-surface-container-high cursor-pointer">
                    <input type="radio" name="attendances[{{ $loop->index }}][status]" value="{{ $key }}" class="hidden" @checked($key === 'H')>
                    {{ $key }}
                </label>
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
    <div class="mt-lg">
        <x-button variant="primary" size="xl" type="submit" icon="send" icon-position="right" class="w-full md:w-auto px-xl py-md" :disabled="!$canEdit">
            Submit Absensi
        </x-button>
    </div>
@endif
</form>
@endsection

@push('scripts')
<script>
    const updateCounts = () => {
        const statuses = [...document.querySelectorAll('.student-card input[type="radio"]:checked')].map(input => input.value);
        document.getElementById('count-present').textContent = statuses.filter(status => status === 'H').length;
        document.getElementById('count-excused').textContent = statuses.filter(status => status === 'S' || status === 'I').length;
        document.getElementById('count-absent').textContent = statuses.filter(status => status === 'A').length;
    };

    document.querySelectorAll('.status-choice input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateCounts();
        });
    });
    updateCounts();
</script>
@endpush