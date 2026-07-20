@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <span class="material-symbols-outlined text-[80px] text-warning">timer_off</span>
    <h1 class="text-headline-xl font-bold text-on-surface mt-md">419</h1>
    <p class="text-body-lg text-on-surface-variant mt-sm">Sesi Berakhir</p>
    <p class="text-body-md text-on-surface-variant mt-xs mb-xl">Halaman kedaluwarsa karena tidak ada aktivitas. Silakan refresh dan coba lagi.</p>
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80">
        <span class="material-symbols-outlined text-[18px]">refresh</span> Muat Ulang
    </a>
</div>
@endsection
