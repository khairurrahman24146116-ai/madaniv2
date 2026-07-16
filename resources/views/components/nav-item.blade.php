@props(['href', 'icon', 'active' => false])
<a href="{{ $href }}"
   class="flex items-center gap-md p-md mx-sm my-xs rounded-lg transition-all @if($active) bg-secondary-container text-on-secondary-container @else text-on-surface-variant hover:bg-surface-container-high @endif">
    <span class="material-symbols-outlined">{{ $icon }}</span>
    <span class="text-label-md">{{ $slot }}</span>
</a>
