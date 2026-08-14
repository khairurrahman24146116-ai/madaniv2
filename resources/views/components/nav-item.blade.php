@props([
    'href' => '#',
    'icon' => '',
    'active' => false,
    'label' => '',
    'iconActive' => null,
    'iconInactive' => null,
    'compact' => true,
])

@php
    $activeIcon = $iconActive ?? $icon;
    $inactiveIcon = $iconInactive ?? $icon;
    $iconToUse = $active ? $activeIcon : $inactiveIcon;
    $iconFill = $active ? 1 : 0;
@endphp

<a href="{{ $href }}"
   class="nav-indicator {{ $active ? 'active' : '' }} flex items-center gap-4 px-4 py-3 my-1 rounded-xl transition-all
          @if($active)
              bg-primary-container text-on-primary-container scale-95
              @else
              text-on-surface-variant hover:bg-surface-container-high
          @endif">
    <span class="material-symbols-outlined text-[24px] shrink-0 transition-transform duration-200 {{ $active ? 'scale-110' : '' }}" style="font-variation-settings: 'FILL' {{ $iconFill }}, 'wght' 400, 'GRAD' 0, 'opsz' 24;">
        {{ $iconToUse }}
    </span>
    <span class="{{ $compact ? 'hidden group-hover:block whitespace-nowrap' : 'block whitespace-nowrap' }} font-label-mono font-medium">{{ $label }}</span>
</a>