@props([
    'variant' => 'primary',
    'size' => 'md',
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
        'primary' => 'bg-primary text-on-primary hover:bg-primary/90 active:bg-primary/80 shadow-sm',
        'secondary' => 'bg-secondary-container text-on-secondary-container hover:bg-secondary-container/90',
        'outline' => 'border border-outline bg-transparent hover:bg-surface-container-high',
        'ghost' => 'bg-transparent hover:bg-surface-container-high',
        'error' => 'bg-error text-on-error hover:bg-error/90',
    ];
    
    $sizes = [
        'sm' => 'px-3 py-1.5 text-label-md gap-1.5',
        'md' => 'px-4 py-2 text-body-md gap-2',
        'lg' => 'px-6 py-2.5 text-body-lg gap-2',
        'xl' => 'px-8 py-3 text-headline-md gap-2.5',
    ];
    
    $iconSizes = [
        'sm' => 'text-[16px]',
        'md' => 'text-[18px]',
        'lg' => 'text-[20px]',
        'xl' => 'text-[22px]',
    ];
    
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed';
    $widthClass = $fullWidth ? 'w-full' : '';
    $variantClass = $variants[$variant] ?? $variants['primary'];
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