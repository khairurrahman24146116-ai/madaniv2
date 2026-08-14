@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-gutter">
    {{-- Welcome Header --}}
    <div class="flex justify-between items-end">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-primary mb-1">
                Selamat {{ now()->format('H') < 12 ? 'Pagi' : (now()->format('H') < 15 ? 'Siang' : 'Malam') }}, {{ auth()->user()->name }}
            </h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant">
                {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </p>
        </div>
    </div>

    {{-- Bento Grid Layout --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-gutter">

        {{-- Today's Schedule Card (8 cols) --}}
        <div class="md:col-span-8 flex flex-col gap-gutter reveal">
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col h-full">
                <div class="px-6 py-4 border-b border-outline-variant bg-surface-container-low flex justify-between items-center">
                    <h3 class="font-title-lg text-title-lg text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary">calendar_today</span>
                        Jadwal Hari Ini
                    </h3>
                    <a href="{{ route('schedules.index') }}" class="font-label-mono text-label-mono text-secondary hover:text-primary transition-colors">LIHAT SEMUA</a>
                </div>
                <div class="flex-1 overflow-x-auto">
                    @forelse($upcomingSchedules ?? [] as $session)
                        @php
                            $status = $session['status'] ?? ($loop->first ? 'berlangsung' : 'menunggu');
                            $isActive = $status === 'berlangsung';
                            $isDone = $status === 'selesai';
                        @endphp
                        <div class="{{ $isActive ? 'bg-secondary-container/20 border-l-4 border-secondary' : 'bg-surface-bright border-l-4 border-transparent' }} {{ $isDone ? 'opacity-75' : '' }} hover:bg-surface-container-low transition-colors">
                            <div class="grid grid-cols-1 md:grid-cols-[1fr_1.2fr_1.6fr_auto_auto] items-center gap-2 px-6 py-4 font-data-table text-data-table">
                                <div class="flex items-center gap-2 {{ $isActive ? 'text-primary font-medium' : 'text-on-surface-variant' }}">
                                    @if($isActive)
                                        <span class="w-2 h-2 rounded-full bg-error animate-pulse"></span>
                                    @endif
                                    {{ $session['time'] }}
                                </div>
                                <div class="{{ $isActive ? 'text-primary font-medium' : 'text-on-surface' }}">Kelas {{ $session['class'] }}</div>
                                <div class="{{ $isActive ? 'text-primary font-medium' : 'text-on-surface' }}">{{ $session['subject'] }}</div>
                                <div>
                                    @if($isActive)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-tertiary-container text-tertiary-fixed border border-tertiary">Sedang Berlangsung</span>
                                    @elseif($isDone)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-surface-container-highest text-on-surface-variant border border-outline-variant">Selesai</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-surface-container-high text-on-surface-variant border border-outline-variant">Menunggu</span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if($isActive)
                                        <a href="{{ route('attendances.form') }}" class="bg-primary hover:bg-primary-container text-on-primary px-4 py-2 rounded-lg font-label-mono text-label-mono transition-colors">Absen Sekarang</a>
                                    @elseif($isDone)
                                        <a href="{{ route('attendances.index') }}" class="inline-flex items-center text-on-surface-variant hover:text-primary transition-colors" title="Rekap">
                                            <span class="material-symbols-outlined">check_circle</span>
                                        </a>
                                    @else
                                        <a href="{{ route('schedules.index') }}" class="inline-flex items-center text-on-surface-variant hover:text-primary transition-colors" title="Detail">
                                            <span class="material-symbols-outlined">more_vert</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center">
                            <p class="font-title-lg text-title-lg text-on-surface">Tidak ada jadwal hari ini</p>
                            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Jadwal sesi berikutnya akan muncul di sini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Summary Cards (4 cols) --}}
        <div class="md:col-span-4 flex flex-col gap-gutter">
            {{-- Attendance Summary --}}
            <div class="reveal stagger-delay bg-primary-container text-on-primary-container rounded-xl p-6 shadow-sm flex flex-col gap-4 relative overflow-hidden" style="--stagger: 0">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-primary/20 blur-2xl"></div>
                <div class="flex items-center gap-3 relative z-10">
                    <span class="material-symbols-outlined p-2 bg-primary rounded-lg text-primary-fixed">rule</span>
                    <h3 class="font-title-lg text-title-lg text-primary-fixed">Tingkat Kehadiran</h3>
                </div>
                <div class="flex items-end justify-between relative z-10">
                    <div>
                        <p class="font-headline-lg text-headline-lg font-bold text-primary-fixed leading-none"><span class="count-up" data-count="{{ $attendanceRate ?? 94 }}">0</span><span class="text-2xl">%</span></p>
                        <p class="font-label-mono text-label-mono mt-1 opacity-80">Rata-rata minggu ini</p>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="material-symbols-outlined text-tertiary-fixed">trending_up</span>
                        <p class="font-label-mono text-label-mono text-tertiary-fixed">+2% dari mgg lalu</p>
                    </div>
                </div>
            </div>

            {{-- Mini stats --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="reveal stagger-delay bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm text-center" style="--stagger: 1">
                    <span class="font-label-mono text-label-mono text-on-surface-variant block mb-1">Sesi Hari Ini</span>
                    <span class="font-title-lg text-title-lg text-primary font-bold count-up" data-count="{{ $todaySessions ?? 0 }}">0</span>
                </div>
                <div class="reveal stagger-delay bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm text-center" style="--stagger: 2">
                    <span class="font-label-mono text-label-mono text-on-surface-variant block mb-1">Siswa</span>
                    <span class="font-title-lg text-title-lg text-error font-bold count-up" data-count="{{ $studentCount ?? 0 }}">0</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- FAB Ambil Absensi --}}
<div class="fixed bottom-20 md:bottom-margin-desktop right-margin-mobile md:right-margin-desktop z-50">
    <a href="{{ route('attendances.form') }}" class="bg-secondary hover:bg-secondary/90 text-on-secondary rounded-full shadow-lg flex items-center gap-3 px-6 py-4 transition-transform hover:scale-105 active:scale-95">
        <span class="material-symbols-outlined text-2xl">how_to_reg</span>
        <span class="font-title-lg text-title-lg">Ambil Absensi</span>
    </a>
</div>
@endsection