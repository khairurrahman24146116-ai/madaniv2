@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <span class="material-symbols-outlined text-[80px] text-on-surface-variant">search_off</span>
    <h1 class="text-headline-xl font-bold text-on-surface mt-md">404</h1>
    <p class="text-body-lg text-on-surface-variant mt-sm">Halaman Tidak Ditemukan</p>
    <p class="text-body-md text-on-surface-variant mt-xs mb-xl">Halaman yang anda cari tidak tersedia atau telah dipindahkan.</p>
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke Dashboard
    </a>
</div>
@endsection
