@props([
    'variant' => 'default', // default, success, warning, error, info, neutral
    'size' => 'md', // sm, md, lg
    'dot' => false,
    'class' => '',
])

@php
    $variants = [
        'default' => 'bg-surface-container text-on-surface-variant border border-outline-variant',
        'success' => 'bg-green-50 text-green-800 dark:bg-green-900/20 dark:text-green-300',
        'warning' => 'bg-amber-50 text-amber-800 dark:bg-amber-900/20 dark:text-amber-300',
        'error' => 'bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300',
        'info' => 'bg-blue-50 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300',
        'neutral' => 'bg-slate-50 text-slate-800 dark:bg-slate-800 dark:text-slate-300',
        // Attendance specific
        'hadir' => 'bg-emerald-50 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300',
        'sakit' => 'bg-amber-50 text-amber-800 dark:bg-amber-900/20 dark:text-amber-300',
        'izin' => 'bg-blue-50 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300',
        'alpa' => 'bg-rose-50 text-rose-800 dark:bg-rose-900/20 dark:text-rose-300',
        // Grade specific
        'lulus' => 'bg-green-50 text-green-800 dark:bg-green-900/20 dark:text-green-300',
        'tidak-lulus' => 'bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300',
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