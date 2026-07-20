@extends('layouts.app')

@section('content')
<div class="mb-lg">
    <h2 class="text-headline-lg-mobile md:text-headline-lg font-bold text-on-surface">Dashboard Wali Murid</h2>
    <p class="text-body-md text-on-surface-variant mt-1">Pantau perkembangan akademik putra/putri anda</p>
</div>

@if($students->isEmpty())
<x-card variant="default" padding="lg" class="text-center">
    <span class="material-symbols-outlined text-[48px] text-on-surface-variant">child_care</span>
    <p class="text-body-md text-on-surface-variant mt-md">Belum ada data siswa terhubung dengan akun ini.</p>
</x-card>
@else
<div class="grid grid-cols-1 md:grid-cols-2 gap-lg">
    @foreach($students as $student)
    <x-card variant="default" padding="lg">
        <div class="flex items-start justify-between mb-md">
            <div>
                <h3 class="text-title-lg text-on-surface font-bold">{{ $student->name }}</h3>
                <p class="text-body-md text-on-surface-variant">NIS: {{ $student->nis }} | {{ $student->classroom->name ?? '-' }}</p>
            </div>
            <span class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container font-bold text-title-lg">
                {{ substr($student->name, 0, 1) }}
            </span>
        </div>
        <div class="grid grid-cols-2 gap-md mb-md">
            <div class="bg-surface-container-high rounded-xl p-md text-center">
                <p class="text-headline-sm font-bold text-primary">{{ $student->attendances()->where('status', 'H')->count() }}</p>
                <p class="text-caption text-on-surface-variant">Hadir</p>
            </div>
            <div class="bg-surface-container-high rounded-xl p-md text-center">
                <p class="text-headline-sm font-bold text-error">{{ $student->attendances()->whereNot('status', 'H')->count() }}</p>
                <p class="text-caption text-on-surface-variant">Tidak Hadir</p>
            </div>
        </div>
        <a href="{{ route('wali-murid.rapor', $student) }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80">
            <span class="material-symbols-outlined text-[18px]">assignment</span> Lihat Rapor
        </a>
    </x-card>
    @endforeach
</div>
@endif
@endsection
