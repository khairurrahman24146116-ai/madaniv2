@extends('layouts.app')

@section('content')
<x-page-header 
    title="Pesan dari {{ $contactMessage->user->name }}" 
    subtitle="Lihat dan balas pesan"
    icon="mail"
    :actions="[
        ['type' => 'button', 'label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('admin.contact.index')],
    ]"
/>

<x-card variant="default" padding="lg" class="mb-6">
    <div class="mb-4">
        <p class="text-label-md text-on-surface-variant">SUBJEK</p>
        <p class="text-headline-md text-on-surface">{{ $contactMessage->subject }}</p>
    </div>
    <div class="mb-4">
        <p class="text-label-md text-on-surface-variant">PENGIRIM</p>
        <p class="text-body-md text-on-surface">{{ $contactMessage->user->name }}</p>
    </div>
    <div>
        <p class="text-label-md text-on-surface-variant">PESAN</p>
        <p class="text-body-lg text-on-surface whitespace-pre-line">{{ $contactMessage->message }}</p>
    </div>
</x-card>

@if($contactMessage->admin_reply)
<x-card variant="default" padding="lg" class="mb-6">
    <p class="text-label-md text-tertiary-container mb-2">BALASAN ANDA</p>
    <p class="text-body-lg text-on-surface whitespace-pre-line">{{ $contactMessage->admin_reply }}</p>
    <p class="text-label-md text-on-surface-variant mt-2">{{ $contactMessage->replied_at ? $contactMessage->replied_at->format('d/m/Y H:i') : '' }}</p>
</x-card>
@endif

@if(!$contactMessage->admin_reply)
<form action="{{ route('admin.contact.reply', $contactMessage) }}" method="POST" class="max-w-2xl space-y-4">
    @csrf
    <x-card variant="default" padding="lg">
        <div>
            <label class="text-label-md text-on-surface-variant block mb-1">BALASAN</label>
            <textarea name="admin_reply" rows="5" required class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-full"></textarea>
            @error('admin_reply') <p class="text-error text-label-md mt-1">{{ $message }}</p> @enderror
        </div>
    </x-card>
    <x-button variant="primary" type="submit" icon="reply">Kirim Balasan</x-button>
</form>
@endif
@endsection
