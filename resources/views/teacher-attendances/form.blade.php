@extends('layouts.app')

@section('content')
<x-page-header 
    title="Absensi Guru" 
    subtitle="Check-in / Check-out kehadiran guru"
    icon="badge"
    :actions="[
        ['type' => 'button', 'label' => 'Riwayat', 'icon' => 'history', 'variant' => 'outline', 'href' => route('teacher.attendances.index')],
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

{{-- Status Card --}}
<x-card variant="elevated" padding="lg" class="mb-lg text-center">
    @if($todayAttendance && $todayAttendance->check_in)
        <div class="w-16 h-16 bg-green-100 text-green-700 rounded-full flex items-center justify-center mx-auto mb-md">
            <span class="material-symbols-outlined text-4xl">check_circle</span>
        </div>
        <h2 class="text-headline-xl text-on-surface mb-xs">Anda Sudah Check-in</h2>
        <p class="text-body-lg text-on-surface-variant mb-lg">
            {{ \Carbon\Carbon::parse($todayAttendance->check_in)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            &middot; Masuk: {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') }}
            @if($todayAttendance->check_out)
                &middot; Keluar: {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i') }}
            @endif
        </p>
        <div class="flex justify-center gap-md">
            @if(!$todayAttendance->check_out)
            <form action="{{ route('teacher.attendances.checkout') }}" method="POST">
                @csrf
                <input type="hidden" name="date" value="{{ now()->format('Y-m-d') }}">
                <x-button variant="secondary" size="lg" type="submit" icon="logout">Check-out</x-button>
            </form>
            @endif
        </div>
    @else
        <div class="w-16 h-16 bg-surface-container text-on-surface-variant rounded-full flex items-center justify-center mx-auto mb-md">
            <span class="material-symbols-outlined text-4xl">person</span>
        </div>
        <h2 class="text-headline-xl text-on-surface mb-xs">Belum Check-in</h2>
        <p class="text-body-lg text-on-surface-variant mb-lg">
            {{ \Carbon\Carbon::parse(now()->format('Y-m-d'))->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
        </p>
        <form action="{{ route('teacher.attendances.checkin') }}" method="POST" class="flex flex-col items-center gap-md">
            @csrf
            <input type="hidden" name="date" value="{{ now()->format('Y-m-d') }}">
            <div>
                <label class="text-label-md text-on-surface-variant block mb-xs">JADWAL (opsional)</label>
                <select name="schedule_id" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-80">
                    <option value="">Tanpa jadwal</option>
                    @foreach($schedules ?? [] as $sched)
                    <option value="{{ $sched->id }}">
                        {{ $sched->teacherSubject->subject->name }} - {{ $sched->teacherSubject->classroom->name }} ({{ $sched->day }}, {{ $sched->start_time }}-{{ $sched->end_time }})
                    </option>
                    @endforeach
                </select>
            </div>
            <x-button variant="primary" size="xl" type="submit" icon="login" icon-position="right">
                Check-in Sekarang
            </x-button>
        </form>
    @endif
</x-card>

{{-- Manual Attendance Form --}}
<x-card variant="default" padding="lg">
    <h3 class="text-headline-md text-on-surface mb-md">Form Absensi Manual</h3>
    <form action="{{ route('teacher.attendances.store') }}" method="POST" class="space-y-md max-w-xl">
        @csrf
        <input type="hidden" name="date" value="{{ now()->format('Y-m-d') }}">
        <div>
            <label class="text-label-md text-on-surface-variant block mb-xs">JADWAL (opsional)</label>
            <select name="schedule_id" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-full">
                <option value="">Tanpa jadwal</option>
                @foreach($schedules ?? [] as $sched)
                <option value="{{ $sched->id }}">
                    {{ $sched->teacherSubject->subject->name }} - {{ $sched->teacherSubject->classroom->name }} ({{ $sched->day }}, {{ $sched->start_time }}-{{ $sched->end_time }})
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-label-md text-on-surface-variant block mb-xs">STATUS</label>
            <div class="flex gap-sm">
                @foreach(['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa'] as $key => $label)
                <label class="flex-1 py-3 px-3 rounded-lg text-label-md text-center border border-outline-variant cursor-pointer hover:bg-surface-container has-[:checked]:bg-primary has-[:checked]:text-white has-[:checked]:border-primary transition-colors">
                    <input type="radio" name="status" value="{{ $key }}" @checked($key === 'H') class="hidden">
                    {{ $key }} - {{ $label }}
                </label>
                @endforeach
            </div>
        </div>
        <div>
            <label class="text-label-md text-on-surface-variant block mb-xs">KETERANGAN</label>
            <input type="text" name="notes" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-full" placeholder="Opsional">
        </div>
        <x-button variant="primary" type="submit" icon="save">Simpan Absensi</x-button>
    </form>
</x-card>
@endsection
