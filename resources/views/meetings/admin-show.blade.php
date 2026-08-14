@extends('layouts.app')

@section('content')
<x-page-header 
    title="Detail Pertemuan" 
    subtitle="{{ $meeting->subject }}"
    icon="event"
    :actions="[
        ['type' => 'button', 'label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('admin.meetings.index')],
    ]"
/>

<x-card variant="default" padding="lg" class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <p class="text-label-md text-on-surface-variant">PEMOHON</p>
            <p class="text-body-lg text-on-surface">{{ $meeting->user->name }}</p>
        </div>
        <div>
            <p class="text-label-md text-on-surface-variant">TANGGAL DIMINTA</p>
            <p class="text-body-lg text-on-surface">{{ $meeting->requested_date->format('d/m/Y') }}</p>
        </div>
        <div>
            <p class="text-label-md text-on-surface-variant">STATUS</p>
            <p class="text-body-lg">
                @if($meeting->status === 'approved')
                    <span class="text-tertiary-container">Disetujui</span>
                @elseif($meeting->status === 'rejected')
                    <span class="text-error">Ditolak</span>
                @else
                    <span class="text-warning">Menunggu</span>
                @endif
            </p>
        </div>
        <div>
            <p class="text-label-md text-on-surface-variant">SUBJEK</p>
            <p class="text-body-lg text-on-surface">{{ $meeting->subject }}</p>
        </div>
    </div>
    <div>
        <p class="text-label-md text-on-surface-variant">DESKRIPSI</p>
        <p class="text-body-lg text-on-surface whitespace-pre-line">{{ $meeting->description }}</p>
    </div>
</x-card>

@if($meeting->status === 'pending')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <form action="{{ route('admin.meetings.approve', $meeting) }}" method="POST">
        @csrf
        <x-card variant="default" padding="lg">
            <h3 class="text-headline-md text-on-surface mb-4">Setujui</h3>
            <p class="text-body-md text-on-surface-variant mb-6">Setujui pertemuan ini dan konfirmasi ke wali murid.</p>
            <x-button variant="primary" type="submit" icon="check_circle">Setujui Pertemuan</x-button>
        </x-card>
    </form>

    <form action="{{ route('admin.meetings.reject', $meeting) }}" method="POST">
        @csrf
        <x-card variant="default" padding="lg">
            <h3 class="text-headline-md text-on-surface mb-4">Tolak</h3>
            <div class="mb-4">
                <label class="text-label-md text-on-surface-variant block mb-1">ALASAN PENOLAKAN</label>
                <textarea name="rejection_reason" rows="3" required class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-full"></textarea>
            </div>
            <x-button variant="error" type="submit" icon="cancel">Tolak Pertemuan</x-button>
        </x-card>
    </form>
</div>
@elseif($meeting->status === 'rejected')
<x-card variant="default" padding="lg">
    <p class="text-label-md text-on-surface-variant">ALASAN PENOLAKAN</p>
    <p class="text-body-lg text-on-surface whitespace-pre-line">{{ $meeting->rejection_reason }}</p>
</x-card>
@endif
@endsection
