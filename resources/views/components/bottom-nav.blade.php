@props([
    'items' => [],
    'class' => '',
])

<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 flex justify-around items-center px-2 py-2 pb-safe bg-surface-container-highest border-t border-outline-variant shadow-lg rounded-t-xl {{ $class }}">
    @foreach($items as $item)
        @php
            $isActive = false;
            if (isset($item['activeRoutes'])) {
                foreach ($item['activeRoutes'] as $route) {
                    if (request()->routeIs($route)) {
                        $isActive = true;
                        break;
                    }
                }
            } elseif (isset($item['href'])) {
                $isActive = request()->routeIs($item['href']);
            }

            $icon = $item['icon'] ?? '';
            $iconActive = $item['iconActive'] ?? $icon;
            $iconInactive = $item['iconInactive'] ?? $icon;
        @endphp

        <a href="{{ $item['href'] ?? '#' }}"
           class="flex flex-col items-center justify-center rounded-full px-4 py-1 transition-colors duration-100
                  @if($isActive)
                      bg-secondary-container text-on-secondary-container
                  @else
                      text-on-surface-variant hover:bg-surface-variant
                  @endif"
           @if(isset($item['title'])) title="{{ $item['title'] }}" @endif>
            <span class="material-symbols-outlined text-[24px] transition-transform duration-200 {{ $isActive ? 'animate-pop' : '' }}" style="font-variation-settings: 'FILL' {{ $isActive ? 1 : 0 }}, 'wght' 400, 'GRAD' 0, 'opsz' 24;">
                {{ $isActive ? $iconActive : $iconInactive }}
            </span>
            @if(isset($item['label']) && $item['label'])
                <span class="font-label-mono text-label-mono mt-1">{{ $item['label'] }}</span>
            @endif
        </a>
    @endforeach
</nav>