@props([
    'title' => 'Madani Al-Aziziyah',
    'subtitle' => 'SMA Sore Dayah Madani',
    'actions' => [], // array of action buttons
    'class' => '',
])

<header class="w-full top-0 sticky bg-surface border-b border-outline-variant z-50 animate-fade-down {{ $class }}">
    <div class="flex justify-between items-center px-margin-mobile md:px-margin-desktop py-2 max-w-[var(--container-max)] mx-auto">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <span class="w-9 h-9 bg-primary text-on-primary rounded-md flex items-center justify-center material-symbols-outlined">school</span>
                <div>
                    <h1 class="font-title-lg text-title-lg font-semibold text-on-surface leading-none">{{ $title }}</h1>
                    <p class="hidden sm:block text-caption text-on-surface-variant mt-1">{{ $subtitle }}</p>
                </div>
            </a>
        </div>
        
        <div class="flex items-center gap-2 md:gap-4">
            @if(auth()->check())
                <span class="text-label-md text-on-surface-variant hidden md:block">{{ auth()->user()->name }}</span>
                
                {{-- User menu --}}
                <div class="relative">
                    <button id="user-menu-toggle" type="button" class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container font-bold border border-outline-variant overflow-hidden hover:ring-2 hover:ring-primary/40 transition-shadow" aria-label="Menu pengguna" aria-haspopup="true" aria-expanded="false">
                        @if(auth()->user()->profile_photo_url)
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="" class="w-full h-full object-cover">
                        @else
                            <span>{{ substr(auth()->user()->name, 0, 1) }}</span>
                        @endif
                    </button>
                    <div id="user-menu" class="hidden absolute right-0 mt-2 w-56 bg-surface-container-lowest border border-outline-variant rounded-lg shadow-lg overflow-hidden origin-top-right animate-scale-in">
                        <div class="px-4 py-2 border-b border-outline-variant">
                            <p class="text-label-md font-semibold text-on-surface truncate">{{ auth()->user()->name }}</p>
                            <p class="text-caption text-on-surface-variant truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 w-full px-4 py-2 text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                            <span class="text-label-md">Profil</span>
                        </a>
                        <form method="POST" action="{{ route('auth.logout.web') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-4 w-full px-4 py-2 text-on-surface-variant hover:bg-surface-container-high hover:text-error transition-colors">
                                <span class="material-symbols-outlined text-[20px]">logout</span>
                                <span class="text-label-md">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
                
                {{-- Hamburger for mobile --}}
                <button id="hamburger-toggle" type="button" class="md:hidden p-2 hover:bg-surface-container-high rounded-md transition-colors" aria-label="Buka menu" aria-expanded="false">
                    <span class="material-symbols-outlined text-on-surface-variant text-[24px]">menu</span>
                </button>
            @endif
            
            {{-- Custom actions --}}
            @foreach($actions as $action)
                @php
                    $aVariant = $action['variant'] ?? 'outlined';
                    $aSize = $action['size'] ?? 'md';
                    $aIcon = $action['icon'] ?? null;
                    $aHref = $action['href'] ?? null;
                    $aDisabled = $action['disabled'] ?? false;
                    $aOnclick = $action['onclick'] ?? null;
                    $aLabel = $action['label'] ?? '';
                    $aTitle = $action['title'] ?? null;
                @endphp
                <x-button :variant="$aVariant" :size="$aSize" :icon="$aIcon" :href="$aHref" :disabled="$aDisabled" :onclick="$aOnclick" :title="$aTitle">
                    {{ $aLabel }}
                </x-button>
            @endforeach
        </div>
    </div>
</header>