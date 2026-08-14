@extends('layouts.app')

@section('content')
@php
    /** @var \Illuminate\Support\Collection $students */
    $student = $students->first();
    $attendanceTotal = ($student?->hadir_count ?? 0) + ($student->{'tidak_hadir_count'} ?? 0);
    $attendancePct = $attendanceTotal > 0
        ? round(($student?->hadir_count ?? 0) / $attendanceTotal * 100)
        : 0;
    $currentFee = $student?->studentFees?->where('month', now()->month)->where('year', now()->year)->first();
    $waliKelas = $student?->classroom?->waliKelas?->name;
@endphp

{{-- Header --}}
<div class="mb-gutter flex justify-between items-end">
    <div>
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary">Dashboard Wali Murid</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-2">
            Memantau perkembangan akademis {{ $student->name ?? 'siswa' }}.
        </p>
    </div>
</div>

{{-- Bento Grid Layout --}}
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-gutter">

    {{-- Student Profile Summary --}}
    <div class="reveal stagger-delay md:col-span-2 lg:col-span-2 bg-surface-container-low rounded-xl p-6 border border-outline-variant shadow-sm flex flex-col justify-between" style="--stagger: 0">
        <div>
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-4">
                    @if ($student && $student->photo_path)
                        <img class="w-16 h-16 rounded-lg object-cover" src="{{ asset('storage/'.$student->photo_path) }}" alt="{{ $student->name }}">
                    @else
                        <div class="w-16 h-16 rounded-lg bg-surface-container-highest flex items-center justify-center">
                            <span class="material-symbols-outlined text-[32px] text-primary">person</span>
                        </div>
                    @endif
                    <div>
                        <h2 class="font-title-lg text-title-lg font-semibold text-primary">{{ $student->name ?? 'Belum pilih siswa' }}</h2>
                        <p class="font-body-md text-body-md text-on-surface-variant">{{ $student->classroom->name ?? '-' }}</p>
                    </div>
                </div>
                @if($student)
                    <span class="px-3 py-1 bg-tertiary-fixed text-on-tertiary-fixed text-xs font-label-mono text-label-mono rounded-full border border-on-tertiary-fixed-variant">
                        {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-4 mt-6">
                <div>
                    <p class="font-label-mono text-label-mono text-outline mb-1">NISN</p>
                    <p class="font-data-table text-data-table font-medium text-on-surface">{{ $student->nisn ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-label-mono text-label-mono text-outline mb-1">Wali Kelas</p>
                    <p class="font-body-md text-body-md font-medium text-on-surface">{{ $waliKelas ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="mt-6 pt-4 border-t border-outline-variant flex gap-3">
            <a href="{{ route('active-letters.index') }}" class="flex-1 flex items-center justify-center gap-2 py-2 bg-primary text-on-primary rounded-lg hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-sm">description</span>
                <span class="font-body-md text-body-md font-medium">Surat Aktif</span>
            </a>
            <a href="{{ route('wali.letters.index') }}" class="flex-1 flex items-center justify-center gap-2 py-2 border border-outline text-primary rounded-lg hover:bg-surface-variant/30 transition-colors">
                <span class="material-symbols-outlined text-sm">history_edu</span>
                <span class="font-body-md text-body-md font-medium">Surat</span>
            </a>
            @if($student)
            <a href="{{ route('wali-murid.rapor', $student->id) }}" class="flex-1 flex items-center justify-center gap-2 py-2 border border-outline text-primary rounded-lg hover:bg-surface-variant/30 transition-colors">
                <span class="material-symbols-outlined text-sm">assignment</span>
                <span class="font-body-md text-body-md font-medium">Lihat Rapor</span>
            </a>
            @endif
        </div>
    </div>

    {{-- Key Metric: Attendance --}}
    <div class="reveal stagger-delay bg-secondary-container text-on-secondary-container rounded-xl p-6 shadow-md flex flex-col justify-between" style="--stagger: 1">
        <div class="flex justify-between items-start">
            <h3 class="font-title-lg text-title-lg font-semibold">Kehadiran</h3>
            <span class="material-symbols-outlined">event_available</span>
        </div>
        <div class="mt-4 flex flex-col items-center">
            <div class="relative w-24 h-24 flex items-center justify-center rounded-full bg-surface/30">
                <svg class="w-full h-full transform -rotate-90 absolute" viewBox="0 0 36 36">
                    <path class="text-surface/30" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3"></path>
                    <path class="text-on-secondary-container" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-dasharray="{{ $attendancePct }}, 100" stroke-linecap="round" stroke-width="3"></path>
                </svg>
                <span class="text-2xl leading-none font-bold z-10 count-up tabular-nums" data-count="{{ $attendancePct }}%">0%</span>
            </div>
        </div>
        <div class="mt-4 text-center font-body-md text-body-md">
            <p>Hadir: {{ $student?->hadir_count ?? 0 }} Hari</p>
            <p class="text-sm opacity-80">Tidak Hadir: {{ $student->{'tidak_hadir_count'} ?? 0 }} Hari</p>
        </div>
    </div>

    {{-- Key Metric: Finance --}}
    <div class="reveal stagger-delay bg-primary-container text-on-primary-container rounded-xl p-6 shadow-md flex flex-col justify-between" style="--stagger: 2">
        <div class="flex justify-between items-start">
            <h3 class="font-title-lg text-title-lg font-semibold">SPP &amp; Keuangan</h3>
            <span class="material-symbols-outlined">payments</span>
        </div>
        <div class="mt-6">
            <p class="font-label-mono text-label-mono opacity-80 mb-1">Tagihan Bulan Ini ({{ now()->locale('id')->monthName }})</p>
            @if ($currentFee)
                <p class="font-headline-lg-mobile text-headline-lg-mobile font-bold font-data-table">
                    Rp {{ number_format((float) $currentFee->amount, 0, ',', '.') }}
                </p>
            @else
                <p class="font-headline-lg-mobile text-headline-lg-mobile font-bold font-data-table">—</p>
            @endif
        </div>
        @if ($currentFee?->is_paid)
            <div class="mt-6 flex items-center gap-2 bg-tertiary-fixed/20 p-2 rounded-lg text-tertiary-fixed">
                <span class="material-symbols-outlined text-sm">check_circle</span>
                <span class="font-body-md text-body-md text-sm">Lunas</span>
            </div>
        @else
            <div class="mt-6 flex items-center gap-2 text-error-container bg-error/20 p-2 rounded-lg">
                <span class="material-symbols-outlined text-sm">warning</span>
                <span class="font-body-md text-body-md text-sm">{{ $currentFee ? 'Belum dibayar' : 'Tidak ada tagihan' }}</span>
            </div>
        @endif
        @if ($student && ! optional($currentFee)->is_paid && $currentFee)
            <a href="{{ route('spp.payer') }}" class="mt-4 w-full py-2 bg-on-primary-container text-primary-container font-semibold rounded-lg hover:bg-opacity-90 transition-opacity flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-sm">payments</span>
                Bayar Sekarang
            </a>
        @endif
    </div>

    {{-- Recent Announcements --}}
    <div class="reveal stagger-delay md:col-span-3 lg:col-span-4 bg-surface-container-lowest rounded-xl p-6 border border-outline-variant shadow-sm" style="--stagger: 3">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-title-lg text-title-lg font-semibold text-primary">Pengumuman Sekolah</h3>
            <span class="text-secondary hover:underline font-body-md text-body-md text-sm">Lihat Semua</span>
        </div>
        <div class="space-y-4">
            <div class="flex gap-4 p-4 hover:bg-surface-container transition-colors rounded-lg border border-transparent hover:border-outline-variant">
                <div class="w-12 h-12 rounded-full bg-primary-fixed flex items-center justify-center text-on-primary-fixed flex-shrink-0">
                    <span class="material-symbols-outlined">campaign</span>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <h4 class="font-body-md text-body-md font-bold text-on-surface">Pertemuan Wali Murid &amp; Pembagian Rapor UTS</h4>
                        <span class="font-label-mono text-label-mono text-xs text-outline">2 Hari yang lalu</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant text-sm line-clamp-2">
                        Diberitahukan kepada seluruh wali murid kelas X, XI, dan XII bahwa pertemuan evaluasi tengah semester dan pengambilan rapor akan dilaksanakan pada hari Sabtu, 18 November 2023.
                    </p>
                </div>
            </div>
            <div class="flex gap-4 p-4 hover:bg-surface-container transition-colors rounded-lg border border-transparent hover:border-outline-variant">
                <div class="w-12 h-12 rounded-full bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed flex-shrink-0">
                    <span class="material-symbols-outlined">school</span>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <h4 class="font-body-md text-body-md font-bold text-on-surface">Jadwal Ujian Akhir Semester (UAS) Ganjil</h4>
                        <span class="font-label-mono text-label-mono text-xs text-outline">5 Hari yang lalu</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant text-sm line-clamp-2">
                        UAS Ganjil Tahun Ajaran 2023/2024 akan diselenggarakan mulai tanggal 4 Desember hingga 15 Desember 2023. Diharapkan seluruh siswa mempersiapkan diri dengan baik.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection