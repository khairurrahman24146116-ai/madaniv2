@extends('layouts.app')

@section('content')
<x-page-header 
    title="Detail Surat" 
    subtitle="{{ $letter->title }}"
    icon="description"
    :actions="[
        ['type' => 'button', 'label' => 'Cetak PDF', 'icon' => 'print', 'variant' => 'primary', 'href' => route('letters.print', $letter)],
        ['type' => 'button', 'label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => url()->previous()],
    ]"
/>

<x-card variant="default" padding="none" class="overflow-hidden">
    {{-- Letter Header --}}
    <div class="p-6 md:p-8 bg-surface-container-low border-b border-outline-variant flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary-container flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[24px]">description</span>
        </div>
        <div class="flex-1">
            <h2 class="text-headline-md text-on-surface">{{ $letter->title }}</h2>
            <p class="font-label-mono text-label-mono text-on-surface-variant mt-1">{{ $letter->type }}</p>
        </div>
        <span class="px-3 py-1 rounded-full bg-primary-fixed/40 text-on-primary-fixed text-label-md shrink-0">
            {{ $letter->type }}
        </span>
    </div>

    {{-- Letter Meta --}}
    <div class="px-6 md:px-8 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4 border-b border-outline-variant">
        <div>
            <p class="text-caption text-on-surface-variant uppercase mb-1">Tanggal</p>
            <p class="text-body-md text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px] text-on-surface-variant">calendar_month</span>
                {{ $letter->created_at->format('d/m/Y') }}
            </p>
        </div>
        @if($letter->user)
        <div>
            <p class="text-caption text-on-surface-variant uppercase mb-1">Dibuat Oleh</p>
            <p class="text-body-md text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px] text-on-surface-variant">person</span>
                {{ $letter->user->name }}
            </p>
        </div>
        @endif
        <div>
            <p class="text-caption text-on-surface-variant uppercase mb-1">Diperbarui</p>
            <p class="text-body-md text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px] text-on-surface-variant">update</span>
                {{ $letter->updated_at->diffForHumans() }}
            </p>
        </div>
    </div>

    {{-- Letter Body --}}
    <div class="p-6 md:p-8">
        <div class="bg-surface-container-low rounded-xl p-6 md:p-8 min-h-[240px]">
            <div class="text-body-lg text-on-surface leading-relaxed whitespace-pre-line">
                {{ $letter->content }}
            </div>
        </div>
    </div>
</x-card>
@endsection