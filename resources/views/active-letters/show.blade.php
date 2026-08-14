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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="text-caption text-on-surface-variant mb-1">NAMA SISWA</p>
            <p class="text-body-md text-on-surface font-semibold">{{ $activeLetter->student->name }}</p>
        </div>
        <div>
            <p class="text-caption text-on-surface-variant mb-1">NIS</p>
            <p class="text-body-md text-on-surface">{{ $activeLetter->student->nis }}</p>
        </div>
        <div>
            <p class="text-caption text-on-surface-variant mb-1">KELAS</p>
            <p class="text-body-md text-on-surface">{{ $activeLetter->student->classroom?->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-caption text-on-surface-variant mb-1">STATUS</p>
            @if($activeLetter->status === 'selesai')
                <span class="text-tertiary-container text-label-md bg-tertiary-fixed/40 px-2 py-0.5 rounded">Selesai</span>
            @elseif($activeLetter->status === 'diambil')
                <span class="text-secondary text-label-md bg-secondary-fixed/40 px-2 py-0.5 rounded">Diambil</span>
            @elseif($activeLetter->status === 'ditolak')
                <span class="text-error text-label-md bg-error-container/50 px-2 py-0.5 rounded">Ditolak</span>
            @else
                <span class="text-warning text-label-md bg-warning-container/50 px-2 py-0.5 rounded">Dalam Proses</span>
            @endif
        </div>
        <div>
            <p class="text-caption text-on-surface-variant mb-1">CEK SPP</p>
            @if($activeLetter->spp_verified)
                <span class="text-tertiary-container text-label-md bg-tertiary-fixed/40 px-2 py-0.5 rounded">Lunas</span>
            @else
                <span class="text-error text-label-md bg-error-container/50 px-2 py-0.5 rounded">Belum Lunas</span>
            @endif
        </div>
        @if($activeLetter->letter_number)
        <div>
            <p class="text-caption text-on-surface-variant mb-1">NOMOR SURAT</p>
            <p class="text-body-md text-on-surface font-mono">{{ $activeLetter->letter_number }}</p>
        </div>
        @endif
        <div>
            <p class="text-caption text-on-surface-variant mb-1">PENGAJU</p>
            <p class="text-body-md text-on-surface">{{ $activeLetter->teacher->name }}</p>
        </div>
        @if($activeLetter->taken_by)
        <div>
            <p class="text-caption text-on-surface-variant mb-1">DIAMBIL OLEH</p>
            <p class="text-body-md text-on-surface">{{ $activeLetter->taker->name }}</p>
        </div>
        <div>
            <p class="text-caption text-on-surface-variant mb-1">TANGGAL DIAMBIL</p>
            <p class="text-body-md text-on-surface">{{ $activeLetter->taken_at->format('d/m/Y H:i') }}</p>
        </div>
        @endif
    </div>

    <hr class="my-6 border-outline-variant">

    <div>
        <p class="text-caption text-on-surface-variant mb-1">KEPERLUAN</p>
        <p class="text-body-md text-on-surface leading-relaxed">{{ $activeLetter->purpose }}</p>
    </div>

    @if($activeLetter->rejected_reason)
    <hr class="my-6 border-outline-variant">
    <div>
        <p class="text-caption text-on-surface-variant mb-1">ALASAN DITOLAK</p>
        <p class="text-body-md text-error">{{ $activeLetter->rejected_reason }}</p>
    </div>
    @endif

    @if($activeLetter->status === 'selesai')
    <div class="flex gap-2 mt-6 pt-6 border-t border-outline-variant">
        <form action="{{ route('active-letters.mark-taken', $activeLetter) }}" method="POST"
            data-confirm="Tandai surat sudah diambil siswa?"
            data-confirm-title="Tandai Diambil"
            data-confirm-variant="info"
            data-confirm-confirm-text="Ya, Tandai">
            @csrf
            <x-button variant="primary" type="submit" icon="handshake">Tandai Diambil</x-button>
        </form>
        <a href="{{ route('active-letters.print', $activeLetter) }}" class="px-6 py-2 border border-outline-variant rounded-lg text-label-md text-on-surface-variant hover:bg-surface-container transition-colors inline-flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">print</span>
            Cetak PDF
        </a>
    </div>
    @endif
</x-card>
@endsection
