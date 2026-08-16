@props([
    'title' => '',
    'subtitle' => null,
    'icon' => null,
    'actions' => [],
    'class' => '',
])

<div class="mb-6 reveal {{ $class }}">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            @if($icon)
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-primary animate-scale-in">{{ $icon }}</span>
                    <h2 class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg font-bold text-primary">{{ $title }}</h2>
                </div>
            @else
                <h2 class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg font-bold text-primary">{{ $title }}</h2>
            @endif
            @if($subtitle)
                <p class="text-body-md text-on-surface-variant mt-1">{{ $subtitle }}</p>
            @endif
        </div>
        @if(!empty($actions))
            <div class="flex flex-wrap gap-2 self-end">
                @foreach(array_values(array_filter($actions, fn ($action) => is_array($action))) as $index => $action)
                    @php
                        $aVariant = $action['variant'] ?? 'primary';
                        $aSize = $action['size'] ?? 'md';
                        $aIcon = $action['icon'] ?? null;
                        $aHref = $action['href'] ?? null;
                        $aDisabled = $action['disabled'] ?? false;
                        $aOnclick = $action['onclick'] ?? null;
                    @endphp
                    <div class="animate-fade-up stagger-delay" style="--stagger: {{ $index + 1 }}">
                        <x-button :variant="$aVariant" :size="$aSize" :icon="$aIcon" :href="$aHref" :disabled="$aDisabled" :onclick="$aOnclick">
                            {{ $action['label'] }}
                        </x-button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>