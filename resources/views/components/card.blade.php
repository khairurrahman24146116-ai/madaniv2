@props([
    'variant' => 'outlined', // elevated, outlined, filled
    'padding' => 'md', // none, sm, md, lg, xl
    'hover' => false,
    'class' => '',
    'borderLeft' => null,
])

@php
    $variants = [
        'elevated' => 'bg-surface-container-low shadow-sm',
        'outlined' => 'bg-surface-container-lowest border border-outline-variant',
        'filled' => 'bg-surface-container-high',
    ];
    
    $paddings = [
        'none' => '',
        'sm' => 'p-3',
        'md' => 'p-4',
        'lg' => 'p-6',
        'xl' => 'p-8',
    ];
    
    $hoverClass = $hover ? 'hover:bg-surface-container-low transition-colors' : '';
    $variantClass = $variants[$variant] ?? $variants['outlined'];
    $paddingClass = $paddings[$padding] ?? $paddings['md'];
    $borderLeftClass = $borderLeft ? "border-l-4 {$borderLeft}" : '';
    $liftClass = $hover ? 'lift' : '';
@endphp

<div class="rounded-lg {{ $variantClass }} {{ $paddingClass }} {{ $hoverClass }} {{ $liftClass }} {{ $borderLeftClass }} {{ $class }}">
    {{ $slot }}
</div>