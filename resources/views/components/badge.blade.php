@props([
    'variant' => 'default', // default, success, warning, error, info, neutral, hadir, sakit, izin, alpa, lulus, tidak-lulus
    'size' => 'md', // sm, md, lg
    'dot' => false,
    'class' => '',
])

@php
    $variants = [
        'default' => 'bg-surface-container text-on-surface-variant border border-outline-variant',
        'success' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
        'warning' => 'bg-warning-container text-on-warning-container border border-warning/30',
        'error' => 'bg-error-container text-on-error-container',
        'info' => 'bg-secondary-fixed text-on-secondary-fixed-variant',
        'neutral' => 'bg-surface-container-high text-on-surface-variant',
        // Attendance specific
        'hadir' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
        'sakit' => 'bg-secondary-fixed text-on-secondary-fixed-variant',
        'izin' => 'bg-primary-fixed text-on-primary-fixed-variant',
        'alpa' => 'bg-error-container text-on-error-container',
        // Grade specific
        'lulus' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
        'tidak-lulus' => 'bg-error-container text-on-error-container',
    ];
    
    $sizes = [
        'sm' => 'px-2 py-0.5 text-[10px] gap-1',
        'md' => 'px-2.5 py-1 text-label-md gap-1.5',
        'lg' => 'px-3 py-1.5 text-body-md gap-2',
    ];
    
    $dotSizes = [
        'sm' => 'w-1.5 h-1.5',
        'md' => 'w-2 h-2',
        'lg' => 'w-2.5 h-2.5',
    ];
    
    $variant = $variants[$variant] ?? $variants['default'];
    $sizeClasses = $sizes[$size];
    $dotSizeClasses = $dotSizes[$size];
@endphp

@if($slot)
    <span class="inline-flex items-center font-medium rounded-full {{ $variant }} {{ $sizeClasses }} {{ $class }}">
        @if($dot)
            <span class="{{ $dotSizeClasses }} rounded-full bg-current opacity-60"></span>
        @endif
        {{ $slot }}
    </span>
@else
    <span class="inline-flex items-center font-medium rounded-full {{ $variant }} {{ $sizeClasses }} {{ $class }}">
        @if($dot)
            <span class="{{ $dotSizeClasses }} rounded-full bg-current opacity-60"></span>
        @endif
        {{-- Self-closing for JS usage --}}
    </span>
@endif