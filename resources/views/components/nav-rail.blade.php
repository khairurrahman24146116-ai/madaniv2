@props([
    'items' => [],
    'activeRoute' => '',
    'class' => '',
])

<aside class="group hidden md:flex h-[calc(100vh-64px)] w-nav-rail-width hover:w-64 transition-all duration-300 flex-col bg-surface-container border-r border-outline-variant fixed left-0 top-16 z-40 overflow-x-hidden overflow-y-auto shadow-sm {{ $class }}">
    <nav class="flex-1 space-y-1 py-4 px-1">
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
                        <p class="hidden group-hover:block text-caption text-on-surface-variant px-3 pb-1 uppercase tracking-wider whitespace-nowrap">{{ $item['label'] }}</p>
                    @endif
                </div>
            @else
                <x-nav-item 
                    href="{{ $item['href'] ?? '#' }}" 
                    :icon="$iconActive" 
                    :iconInactive="$iconInactive"
                    :active="$isActive"
                    label="{{ $item['label'] ?? '' }}"
                    :compact="true"
                />
            @endif
        @endforeach
    </nav>
    
    <div class="p-4 mt-auto border-t border-outline-variant">
        <form method="POST" action="{{ route('auth.logout.web') }}">
            @csrf
            <button type="submit" class="flex items-center justify-center group-hover:justify-start gap-4 px-4 py-3 my-1 mx-1 text-on-surface-variant hover:text-error transition-colors w-full">
                <span class="material-symbols-outlined text-[20px] shrink-0">logout</span>
                <span class="hidden group-hover:block whitespace-nowrap font-label-mono">Logout</span>
            </button>
        </form>
    </div>
</aside>