@props([
    'components' => ['tugas', 'ph', 'uts', 'uas'],
    'active' => 'tugas',
    'class' => '',
])

<div class="inline-flex bg-surface-container-high p-1 rounded-xl shadow-inner border border-outline-variant {{ $class }}">
    @foreach($components as $component)
        @php
            $labels = ['tugas' => 'Tugas', 'ph' => 'Harian', 'uts' => 'UTS', 'uas' => 'UAS'];
        @endphp
        <button type="button" 
                data-component="{{ $component }}" 
                class="score-component px-6 py-2 rounded-lg text-label-md transition-all
                       @if($component === $active) bg-primary text-on-primary shadow-sm @else text-on-surface-variant hover:bg-surface-container @endif">
            {{ $labels[$component] ?? ucfirst($component) }}
        </button>
    @endforeach
</div>