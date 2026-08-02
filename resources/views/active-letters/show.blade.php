@extends('layouts.app')

@section('content')
<x-page-header 
    title="Detail Surat Aktif" 
    subtitle="{{ $activeLetter->student->name }}"
    icon="badge"
    :actions="[
        $activeLetter->status === 'selesai' || $activeLetter->status === 'diambil' 
            ? ['type' => 'button', 'label' => 'Cetak PDF', 'icon' => 'print', 'variant' => 'primary', 'href' => route('active-letters.print', $activeLetter)]
            : null,
        ['type' => 'button', 'label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('active-letters.index')],
    ]"
/>

<x-card variant="default" padding="lg">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-lg">
        <div>
            <p class="text-caption text-on-surface-variant mb-xs">NAMA SISWA</p>
            <p class="text-body-md text-on-surface font-semibold">{{ $activeLetter->student->name }}</p>
        </div>
        <div>
            <p class="text-caption text-on-surface-variant mb-xs">NIS</p>
            <p class="text-body-md text-on-surface">{{ $activeLetter->student->nis }}</p>
        </div>
        <div>
            <p class="text-caption text-on-surface-variant mb-xs">KELAS</p>
            <p class="text-body-md text-on-surface">{{ $activeLetter->student->classroom?->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-caption text-on-surface-variant mb-xs">STATUS</p>
            @if($activeLetter->status === 'selesai')
                <span class="text-green-700 text-label-md bg-green-50 px-sm py-0.5 rounded">Selesai</span>
            @elseif($activeLetter->status === 'diambil')
                <span class="text-blue-700 text-label-md bg-blue-50 px-sm py-0.5 rounded">Diambil</span>
            @elseif($activeLetter->status === 'ditolak')
                <span class="text-red-700 text-label-md bg-red-50 px-sm py-0.5 rounded">Ditolak</span>
            @endif
        </div>
        <div>
            <p class="text-caption text-on-surface-variant mb-xs">CEK SPP</p>
            @if($activeLetter->spp_verified)
                <span class="text-green-700 text-label-md bg-green-50 px-sm py-0.5 rounded">Lunas</span>
            @else
                <span class="text-red-700 text-label-md bg-red-50 px-sm py-0.5 rounded">Belum Lunas</span>
            @endif
        </div>
        @if($activeLetter->letter_number)
        <div>
            <p class="text-caption text-on-surface-variant mb-xs">NOMOR SURAT</p>
            <p class="text-body-md text-on-surface font-mono">{{ $activeLetter->letter_number }}</p>
        </div>
        @endif
        <div>
            <p class="text-caption text-on-surface-variant mb-xs">PENGAJU</p>
            <p class="text-body-md text-on-surface">{{ $activeLetter->teacher->name }}</p>
        </div>
        @if($activeLetter->taken_by)
        <div>
            <p class="text-caption text-on-surface-variant mb-xs">DIAMBIL OLEH</p>
            <p class="text-body-md text-on-surface">{{ $activeLetter->taker->name }}</p>
        </div>
        <div>
            <p class="text-caption text-on-surface-variant mb-xs">TANGGAL DIAMBIL</p>
            <p class="text-body-md text-on-surface">{{ $activeLetter->taken_at->format('d/m/Y H:i') }}</p>
        </div>
        @endif
    </div>

    <hr class="my-lg border-outline-variant">

    <div>
        <p class="text-caption text-on-surface-variant mb-xs">KEPERLUAN</p>
        <p class="text-body-md text-on-surface leading-relaxed">{{ $activeLetter->purpose }}</p>
    </div>

    @if($activeLetter->rejected_reason)
    <hr class="my-lg border-outline-variant">
    <div>
        <p class="text-caption text-on-surface-variant mb-xs">ALASAN DITOLAK</p>
        <p class="text-body-md text-error">{{ $activeLetter->rejected_reason }}</p>
    </div>
    @endif

    @if($activeLetter->status === 'selesai')
    <div class="flex gap-sm mt-lg pt-lg border-t border-outline-variant">
        <form action="{{ route('active-letters.mark-taken', $activeLetter) }}" method="POST" onsubmit="return confirm('Tandai surat sudah diambil siswa?')">
            @csrf
            <x-button variant="primary" type="submit" icon="handshake">Tandai Diambil</x-button>
        </form>
        <a href="{{ route('active-letters.print', $activeLetter) }}" class="px-lg py-2 border border-outline-variant rounded-lg text-label-md text-on-surface-variant hover:bg-surface-container transition-colors inline-flex items-center gap-sm">
            <span class="material-symbols-outlined text-[18px]">print</span>
            Cetak PDF
        </a>
    </div>
    @endif
</x-card>
@endsection
