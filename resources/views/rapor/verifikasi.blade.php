@extends('layouts.guest')

@section('content')
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-8 text-center">
    @if($valid)
        <div class="w-16 h-16 mx-auto rounded-full bg-tertiary-fixed flex items-center justify-center text-on-tertiary-fixed mb-4">
            <span class="material-symbols-outlined text-[32px]">verified</span>
        </div>
        <h1 class="text-headline-lg font-bold text-on-surface">Dokumen Asli</h1>
        <p class="text-body-md text-on-surface-variant mt-2">
            Rapor ini sah dan dikeluarkan oleh <strong>{{ config('school.identity.nama_sekolah') }}</strong>.
        </p>

        <div class="mt-6 pt-4 border-t border-outline-variant text-left space-y-2">
            <div class="flex justify-between text-body-md">
                <span class="text-on-surface-variant">Nama Santri</span>
                <span class="font-semibold text-on-surface">{{ $student?->name ?? '-' }}</span>
            </div>
            <div class="flex justify-between text-body-md">
                <span class="text-on-surface-variant">NIS</span>
                <span class="font-semibold text-on-surface">{{ $student?->nis ?? '-' }}</span>
            </div>
            <div class="flex justify-between text-body-md">
                <span class="text-on-surface-variant">Kelas</span>
                <span class="font-semibold text-on-surface">{{ $student?->classroom?->name ?? '-' }}</span>
            </div>
            <div class="flex justify-between text-body-md">
                <span class="text-on-surface-variant">Periode</span>
                <span class="font-semibold text-on-surface">{{ ucfirst($semester) }} {{ $academicYear }}</span>
            </div>
            <div class="flex justify-between text-body-md">
                <span class="text-on-surface-variant">Rata-Rata NA</span>
                <span class="font-semibold text-on-surface">{{ $overallAverage !== null ? number_format($overallAverage, 2) : '-' }}</span>
            </div>
        </div>
    @else
        <div class="w-16 h-16 mx-auto rounded-full bg-error-container flex items-center justify-center text-on-error-container mb-4">
            <span class="material-symbols-outlined text-[32px]">gpp_bad</span>
        </div>
        <h1 class="text-headline-lg font-bold text-on-surface">Kode Tidak Valid</h1>
        <p class="text-body-md text-on-surface-variant mt-2">
            Kode verifikasi tidak dikenali atau dokumen rapor telah dimodifikasi. Hubungi
            <strong>{{ config('school.identity.nama_sekolah') }}</strong> untuk memastikan keasliannya.
        </p>
    @endif
</div>
@endsection