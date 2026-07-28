@extends('layouts.app')

@section('content')
<x-page-header 
    title="Dashboard" 
    subtitle="Ringkasan aktivitas hari ini"
    icon="dashboard"
    :actions="[
        ['type' => 'button', 'label' => 'Absensi', 'icon' => 'how_to_reg', 'variant' => 'primary', 'href' => route('attendances.form')],
        ['type' => 'button', 'label' => 'Input Nilai', 'icon' => 'grade', 'variant' => 'secondary', 'href' => route('scores.create')],
    ]"
/>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-lg">

    {{-- Active Class Card --}}
    <div class="lg:col-span-8">
        <x-card variant="elevated" padding="lg" class="h-full flex flex-col justify-between">
            <div class="flex justify-between items-start mb-lg">
                <div>
                    <span class="inline-block px-sm py-xs bg-secondary-container text-on-secondary-container rounded text-label-md mb-sm">BERLANGSUNG</span>
                    <h3 class="text-headline-xl text-primary mb-xs">{{ $activeClass ?? 'Fisika - XI MIPA 2' }}</h3>
                    <p class="text-body-lg text-on-surface-variant flex items-center gap-xs">
                        <span class="material-symbols-outlined text-[20px]">location_on</span>
                        {{ $activeRoom ?? 'Lab Fisika Utama, Gedung B' }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-label-md text-on-surface-variant">Sisa Waktu</p>
                    <p class="text-headline-lg text-error font-mono" id="timer">01:42:15</p>
                </div>
            </div>
            
            <div class="bg-surface-container-low p-md rounded-md mb-lg flex items-center justify-between">
                <div class="flex items-center gap-md">
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-200"></div>
                        <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-300"></div>
                        <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-400"></div>
                        <div class="w-8 h-8 rounded-full border-2 border-white bg-primary text-[10px] flex items-center justify-center text-white">+28</div>
                    </div>
                    <p class="text-body-md text-on-surface-variant">{{ $studentCount ?? '31' }} Siswa terdaftar</p>
                </div>
                <div class="w-1/3 bg-outline-variant h-1 rounded-full overflow-hidden">
                    <div class="bg-primary h-full w-[15%]"></div>
                </div>
            </div>
            
            <div class="flex gap-md">
                <x-button variant="primary" size="lg" icon="how_to_reg" href="{{ route('attendances.form') }}" class="flex-1">
                    Buka Absensi
                </x-button>
                <x-button variant="outline" size="lg" class="px-lg">
                    Materi
                </x-button>
            </div>
        </x-card>
    </div>

    {{-- Quick Stats --}}
    <div class="lg:col-span-4 flex flex-col gap-md">
        <x-card variant="default" padding="md" class="flex items-center gap-md">
            <div class="w-12 h-12 bg-secondary-container text-on-secondary-container rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined">class</span>
            </div>
            <div>
                <p class="text-label-md text-on-surface-variant">Kelas Hari Ini</p>
                <p class="text-headline-lg">{{ $todaySessions ?? '4' }} Sesi</p>
            </div>
        </x-card>
        
        <x-card variant="default" padding="md" class="flex items-center gap-md">
            <div class="w-12 h-12 bg-tertiary-container text-on-tertiary-container rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined">analytics</span>
            </div>
            <div>
                <p class="text-label-md text-on-surface-variant">Rata-rata Kehadiran</p>
                <p class="text-headline-lg">{{ $attendanceRate ?? '94.2' }}%</p>
            </div>
        </x-card>
        
        <x-card variant="default" padding="md" class="flex items-center gap-md">
            <div class="w-12 h-12 bg-error-container text-on-error-container rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined">pending_actions</span>
            </div>
            <div>
                <p class="text-label-md text-on-surface-variant">Tugas Perlu Dinilai</p>
                <p class="text-headline-lg">{{ $pendingGrades ?? '12' }} Tugas</p>
            </div>
        </x-card>
    </div>

    {{-- Upcoming Schedule --}}
    <div class="lg:col-span-12 mt-md">
        <h3 class="text-headline-lg text-on-surface mb-lg flex items-center gap-sm">
            <span class="material-symbols-outlined text-primary">event_note</span>
            Jadwal Sesi Mendatang
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-md">
            @forelse($upcomingSchedules ?? [] as $schedule)
                <x-schedule-card :schedule="$schedule" variant="default" />
            @empty
                <x-card variant="default" padding="md" class="md:col-span-3 text-center">
                    <h4 class="text-headline-md text-on-surface mb-xs">Tidak ada jadwal tersisa hari ini</h4>
                    <p class="text-body-md text-on-surface-variant">Jadwal sesi besok akan muncul di sini.</p>
                </x-card>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateTimer() {
        const timerEl = document.getElementById('timer');
        if (!timerEl) return;
        let [h, m, s] = timerEl.innerText.split(':').map(Number);
        setInterval(() => {
            s--;
            if (s < 0) { s = 59; m--; }
            if (m < 0) { m = 59; h--; }
            timerEl.innerText = [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
        }, 1000);
    }
    updateTimer();
</script>
@endpush