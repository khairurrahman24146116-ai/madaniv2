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
            'bg' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'icon' => 'check_circle',
        ],
        'S' => [
            'label' => 'Sakit',
            'short' => 'S',
            'bg' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
            'icon' => 'healing',
        ],
        'I' => [
            'label' => 'Izin',
            'short' => 'I',
            'bg' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'icon' => 'event_note',
        ],
        'A' => [
            'label' => 'Alpa',
            'short' => 'A',
            'bg' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
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

<span class="inline-flex items-center gap-1 font-semibold rounded-full {{ $config['bg'] }} {{ $sizeClass }} {{ $class }}">
    <span class="material-symbols-outlined {{ $iconClass }}">{{ $config['icon'] }}</span>
    @if($showLabel)
        {{ $config['label'] }}
    @else
        {{ $config['short'] }}
    @endif
</span>