@props([
    'variant' => 'filled', // filled/primary, tonal, outlined/outline, secondary, error, ghost
    'size' => 'md', // sm, md, lg, xl
    'type' => 'button',
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'fullWidth' => false,
    'class' => '',
    'href' => null,
    'onclick' => null,
])

@php
    $variants = [
        'filled' => 'bg-primary text-on-primary hover:bg-primary/90 active:bg-primary/80 shadow-sm',
        'primary' => 'bg-primary text-on-primary hover:bg-primary/90 active:bg-primary/80 shadow-sm',
        'tonal' => 'bg-secondary-container text-on-secondary-container hover:bg-secondary-container/90 active:bg-secondary-container/80',
        'secondary' => 'bg-secondary text-on-secondary hover:bg-secondary/90 active:bg-secondary/80',
        'outlined' => 'border border-outline bg-transparent hover:bg-surface-container-high active:bg-surface-container',
        'outline' => 'border border-outline bg-transparent hover:bg-surface-container-high active:bg-surface-container',
        'error' => 'bg-error text-on-error hover:bg-error/90 active:bg-error/80',
        'ghost' => 'bg-transparent text-primary hover:bg-primary/5 active:bg-primary/10',
    ];
    
    $sizes = [
        'sm' => 'px-3 py-1.5 text-label-md gap-1.5',
        'md' => 'px-4 py-2 text-body-md gap-2',
        'lg' => 'px-6 py-2.5 text-body-lg gap-2',
        'xl' => 'px-8 py-3 text-title-lg gap-2.5',
    ];
    
    $iconSizes = [
        'sm' => 'text-[16px]',
        'md' => 'text-[18px]',
        'lg' => 'text-[20px]',
        'xl' => 'text-[22px]',
    ];
    
    $baseClasses = 'btn-press ripple inline-flex items-center justify-center font-medium rounded-md transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary';
    $widthClass = $fullWidth ? 'w-full' : '';
    $variantClass = $variants[$variant] ?? $variants['filled'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $iconClass = $iconSizes[$size] ?? $iconSizes['md'];
@endphp

@if($href)
    <a href="{{ $href }}"
       class="{{ $baseClasses }} {{ $variantClass }} {{ $sizeClass }} {{ $widthClass }} {{ $class }}"
       @if($disabled) tabindex="-1" aria-disabled="true" @endif
       @if($onclick) onclick="{{ $onclick }}" @endif>
        @if($icon && $iconPosition === 'left')
            <span class="material-symbols-outlined {{ $iconClass }}">{{ $icon }}</span>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <span class="material-symbols-outlined {{ $iconClass }}">{{ $icon }}</span>
        @endif
    </a>
@else
    <button type="{{ $type }}"
            {{ $attributes->class([$baseClasses, $variantClass, $sizeClass, $widthClass])->merge(['class' => $class]) }}
            @if($disabled) disabled @endif
            @if($onclick) onclick="{{ $onclick }}" @endif>
        @if($icon && $iconPosition === 'left')
            <span class="material-symbols-outlined {{ $iconClass }}">{{ $icon }}</span>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <span class="material-symbols-outlined {{ $iconClass }}">{{ $icon }}</span>
        @endif
    </button>
@endif