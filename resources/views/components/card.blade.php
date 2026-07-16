@props([
    'variant' => 'default', // default, elevated, outlined, filled
    'padding' => 'md', // none, sm, md, lg, xl
    'hover' => false,
    'class' => '',
    'borderLeft' => null, // color class for left border accent
])

@php
    $variants = [
        'default' => 'bg-surface-container-lowest border border-outline-variant',
        'elevated' => 'bg-surface-container-low shadow-sm',
        'outlined' => 'bg-surface-container-lowest border-2 border-outline',
        'filled' => 'bg-surface-container-high',
    ];
    
    $paddings = [
        'none' => '',
        'sm' => 'p-sm',
        'md' => 'p-md',
        'lg' => 'p-lg',
        'xl' => 'p-xl',
    ];
    
    $hoverClass = $hover ? 'hover:bg-surface-container-low dark:hover:bg-surface-container transition-colors' : '';
    $variantClass = $variants[$variant] ?? $variants['default'];
    $paddingClass = $paddings[$padding] ?? $paddings['md'];
    $borderLeftClass = $borderLeft ? "border-l-4 {$borderLeft}" : '';
    
    // Dark mode adjustments
    $darkAdjustments = 'dark:border-outline dark:shadow-none';
    if ($variant === 'default') {
        $darkAdjustments .= ' dark:border-t-2 dark:border-primary';
    }
@endphp

<div class="rounded-lg {{ $variantClass }} {{ $paddingClass }} {{ $hoverClass }} {{ $borderLeftClass }} {{ $darkAdjustments }} {{ $class }}">
    {{ $slot }}
</div>