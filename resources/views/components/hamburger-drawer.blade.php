@props([
    'items' => [],
    'class' => '',
])

<div id="hamburger-drawer" class="md:hidden fixed inset-0 z-50 hidden {{ $class }}">
    <div id="hamburger-overlay" class="absolute inset-0 bg-black/40 animate-fade-in"></div>
    <div class="absolute left-0 top-0 h-full w-72 bg-surface-container-lowest shadow-xl border-r border-outline-variant overflow-y-auto animate-slide-in-left">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
            <span class="font-title-lg text-title-lg font-semibold text-on-surface">Menu</span>
            <button id="hamburger-close" type="button" class="p-1 text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <nav class="py-4 space-y-1">
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
                
                @if(isset($item['divider']) && $item['divider'])
                    <div class="pt-4 mt-4 border-t border-outline-variant">
                        @if(isset($item['label']))
                            <p class="text-caption text-on-surface-variant px-6 pb-1 uppercase tracking-wider">{{ $item['label'] }}</p>
                        @endif
                    </div>
                @else
                    <x-nav-item 
                        href="{{ $item['href'] ?? '#' }}" 
                        :icon="$iconActive" 
                        :iconInactive="$iconInactive"
                        :active="$isActive"
                        label="{{ $item['label'] ?? '' }}"
                        :compact="false"
                    />
                @endif
            @endforeach
        </nav>
        <div class="p-4 border-t border-outline-variant">
            <form method="POST" action="{{ route('auth.logout.web') }}">
                @csrf
                <button type="submit" class="flex items-center gap-4 w-full px-2 py-2 text-on-surface-variant hover:text-error transition-colors">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    <span class="text-label-md">Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>