@props([
    'variant' => 'default', // default, primary, secondary, success, warning, error, outline
    'size' => 'md', // sm, md, lg
    'icon' => null,
    'iconPosition' => 'left',
    'clickable' => false,
    'href' => null,
    'onclick' => null,
    'class' => '',
])

@php
    $variants = [
        'default' => 'bg-surface-container-high text-on-surface-variant border border-outline-variant',
        'primary' => 'bg-primary-container text-on-primary-container',
        'secondary' => 'bg-secondary-container text-on-secondary-container',
        'success' => 'bg-tertiary-container text-on-tertiary-container',
        'warning' => 'bg-warning-container text-on-warning-container',
        'error' => 'bg-error-container text-on-error-container',
        'outline' => 'bg-transparent text-on-surface-variant border border-outline',
    ];
    
    $sizes = [
        'sm' => 'px-2 py-0.5 text-label-mono gap-1',
        'md' => 'px-3 py-1 text-label-md gap-1.5',
        'lg' => 'px-4 py-1.5 text-body-md gap-2',
    ];
    
    $iconSizes = [
        'sm' => 'text-[12px]',
        'md' => 'text-[14px]',
        'lg' => 'text-[16px]',
    ];
    
    $variantClass = $variants[$variant] ?? $variants['default'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $iconClass = $iconSizes[$size] ?? $iconSizes['md'];
    $clickableClass = $clickable ? 'cursor-pointer hover:opacity-80 active:opacity-60 transition-opacity' : '';
    $baseClass = 'inline-flex items-center font-medium rounded-full';
@endphp

@if($href)
    <a href="{{ $href }}"
       class="{{ $baseClass }} {{ $variantClass }} {{ $sizeClass }} {{ $clickableClass }} {{ $class }}"
       @if($onclick) onclick="{{ $onclick }}" @endif>
        @if($icon && $iconPosition === 'left')
            <span class="material-symbols-outlined {{ $iconClass }}">{{ $icon }}</span>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <span class="material-symbols-outlined {{ $iconClass }}">{{ $icon }}</span>
        @endif
    </a>
@elseif($clickable)
    <button type="button"
            class="{{ $baseClass }} {{ $variantClass }} {{ $sizeClass }} {{ $clickableClass }} {{ $class }}"
            @if($onclick) onclick="{{ $onclick }}" @endif>
        @if($icon && $iconPosition === 'left')
            <span class="material-symbols-outlined {{ $iconClass }}">{{ $icon }}</span>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <span class="material-symbols-outlined {{ $iconClass }}">{{ $icon }}</span>
        @endif
    </button>
@else
    <span class="{{ $baseClass }} {{ $variantClass }} {{ $sizeClass }} {{ $class }}">
        @if($icon && $iconPosition === 'left')
            <span class="material-symbols-outlined {{ $iconClass }}">{{ $icon }}</span>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <span class="material-symbols-outlined {{ $iconClass }}">{{ $icon }}</span>
        @endif
    </span>
@endif