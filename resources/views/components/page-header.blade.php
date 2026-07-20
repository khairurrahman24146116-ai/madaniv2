@props([
    'title' => '',
    'subtitle' => null,
    'icon' => null,
    'actions' => [],
    'class' => '',
])

<div class="mb-6 {{ $class }}">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            @if($icon)
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-primary">{{ $icon }}</span>
                    <h2 class="text-2xl font-bold text-on-surface tracking-tight">{{ $title }}</h2>
                </div>
            @else
                <h2 class="text-2xl font-bold text-on-surface tracking-tight">{{ $title }}</h2>
            @endif
            @if($subtitle)
                <p class="text-on-surface-variant text-sm mt-1">{{ $subtitle }}</p>
            @endif
        </div>
        @if(!empty($actions))
            <div class="flex flex-wrap gap-2 self-end">
                @foreach($actions as $action)
                    @php
                        $aVariant = $action['variant'] ?? 'primary';
                        $aSize = $action['size'] ?? 'md';
                        $aIcon = $action['icon'] ?? null;
                        $aHref = $action['href'] ?? null;
                        $aDisabled = $action['disabled'] ?? false;
                        $aOnclick = $action['onclick'] ?? null;
                    @endphp
                    <x-button :variant="$aVariant" :size="$aSize" :icon="$aIcon" :href="$aHref" :disabled="$aDisabled" :onclick="$aOnclick">
                        {{ $action['label'] }}
                    </x-button>
                @endforeach
            </div>
        @endif
    </div>
</div>