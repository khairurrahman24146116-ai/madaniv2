@extends('layouts.app')

@section('content')
<x-page-header 
    title="Minta Pertemuan" 
    subtitle="Ajukan pertemuan dengan kepala sekolah"
    icon="event"
    :actions="[
        ['type' => 'button', 'label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('wali.meetings.index')],
    ]"
/>

<form action="{{ route('wali.meetings.store') }}" method="POST" class="max-w-2xl space-y-md">
    @csrf
    <x-card variant="default" padding="lg">
        <div class="space-y-md">
            <div>
                <label class="text-label-md text-on-surface-variant block mb-xs">SUBJEK PERTEMUAN</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-full">
                @error('subject') <p class="text-error text-label-md mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-label-md text-on-surface-variant block mb-xs">TANGGAL YANG DIMINTA</label>
                <input type="date" name="requested_date" value="{{ old('requested_date') }}" required class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-full">
                @error('requested_date') <p class="text-error text-label-md mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-label-md text-on-surface-variant block mb-xs">DESKRIPSI / KEPERLUAN</label>
                <textarea name="description" rows="5" required class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-full">{{ old('description') }}</textarea>
                @error('description') <p class="text-error text-label-md mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </x-card>
    <div class="flex gap-sm">
        <x-button variant="primary" type="submit" icon="send">Ajukan Pertemuan</x-button>
        <a href="{{ route('wali.meetings.index') }}" class="px-lg py-2 border border-outline-variant rounded-lg text-label-md text-on-surface-variant hover:bg-surface-container transition-colors inline-flex items-center">Batal</a>
    </div>
</form>
@endsection
