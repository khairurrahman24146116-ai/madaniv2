@extends('layouts.app')

@section('content')
<x-page-header 
    title="{{ $letter->title }}" 
    subtitle="Detail surat"
    icon="description"
    :actions="[
        ['type' => 'button', 'label' => 'Cetak PDF', 'icon' => 'print', 'variant' => 'primary', 'href' => route('letters.print', $letter)],
        ['type' => 'button', 'label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => url()->previous()],
    ]"
/>

<x-card variant="default" padding="lg">
    <div class="flex items-center gap-sm mb-md text-body-md text-on-surface-variant">
        <span class="px-sm py-0.5 rounded text-label-md bg-surface-container-high">{{ $letter->type }}</span>
        <span>&middot;</span>
        <span>{{ $letter->created_at->format('d/m/Y') }}</span>
        @if($letter->user)
            <span>&middot;</span>
            <span>Oleh: {{ $letter->user->name }}</span>
        @endif
    </div>
    <div class="text-body-lg text-on-surface leading-relaxed whitespace-pre-line">
        {{ $letter->content }}
    </div>
</x-card>
@endsection
