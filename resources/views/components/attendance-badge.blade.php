@props([
    'status' => 'H', // H, S, I, A
    'size' => 'md', // sm, md, lg
    'showLabel' => true,
    'class' => '',
])

@php
    $statusConfig = [
        'H' => [
            'label' => 'Hadir',
            'short' => 'H',
            'bg' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
            'icon' => 'check_circle',
        ],
        'S' => [
            'label' => 'Sakit',
            'short' => 'S',
            'bg' => 'bg-secondary-fixed text-on-secondary-fixed-variant',
            'icon' => 'healing',
        ],
        'I' => [
            'label' => 'Izin',
            'short' => 'I',
            'bg' => 'bg-primary-fixed text-on-primary-fixed-variant',
            'icon' => 'event_note',
        ],
        'A' => [
            'label' => 'Alpa',
            'short' => 'A',
            'bg' => 'bg-error-container text-on-error-container',
            'icon' => 'cancel',
        ],
    ];
    
    $sizes = [
        'sm' => 'px-2 py-0.5 text-[10px]',
        'md' => 'px-2.5 py-1 text-label-md',
        'lg' => 'px-3 py-1.5 text-body-md',
    ];
    
    $iconSizes = [
        'sm' => 'text-[10px]',
        'md' => 'text-label-md',
        'lg' => 'text-body-md',
    ];
    
    $config = $statusConfig[$status] ?? $statusConfig['H'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $iconClass = $iconSizes[$size] ?? $iconSizes['md'];
@endphp

<span class="inline-flex items-center gap-1 font-medium rounded-full {{ $config['bg'] }} {{ $sizeClass }} {{ $class }}">
    <span class="material-symbols-outlined {{ $iconClass }}">{{ $config['icon'] }}</span>
    @if($showLabel)
        {{ $config['label'] }}
    @else
        {{ $config['short'] }}
    @endif
</span>