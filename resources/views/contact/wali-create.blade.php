@extends('layouts.app')

@section('content')
<x-page-header 
    title="Pesan Baru" 
    subtitle="Kirim pesan ke kepala sekolah"
    icon="mail"
    :actions="[
        ['type' => 'button', 'label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('wali.contact.index')],
    ]"
/>

<form action="{{ route('wali.contact.store') }}" method="POST" class="max-w-2xl space-y-4">
    @csrf
    <x-card variant="default" padding="lg">
        <div class="space-y-4">
            <div>
                <label class="text-label-md text-on-surface-variant block mb-1">SUBJEK</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-full">
                @error('subject') <p class="text-error text-label-md mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-label-md text-on-surface-variant block mb-1">PESAN</label>
                <textarea name="message" rows="6" required class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-full">{{ old('message') }}</textarea>
                @error('message') <p class="text-error text-label-md mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </x-card>
    <div class="flex gap-2">
        <x-button variant="primary" type="submit" icon="send">Kirim Pesan</x-button>
        <a href="{{ route('wali.contact.index') }}" class="px-6 py-2 border border-outline-variant rounded-lg text-label-md text-on-surface-variant hover:bg-surface-container transition-colors inline-flex items-center">Batal</a>
    </div>
</form>
@endsection
