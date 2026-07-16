@props([
    'value' => null,
    'studentId' => null,
    'component' => 'tugas', // tugas, ph, uts, uas
    'min' => 0,
    'max' => 100,
    'disabled' => false,
    'class' => '',
])

@php
    $componentLabels = [
        'tugas' => 'Tugas',
        'ph' => 'Harian',
        'uts' => 'UTS',
        'uas' => 'UAS',
    ];
    
    $componentColors = [
        'tugas' => 'border-primary/30 focus:border-primary focus:ring-primary/25',
        'ph' => 'border-blue-300 focus:border-blue-500 focus:ring-blue-500/25',
        'uts' => 'border-amber-300 focus:border-amber-500 focus:ring-amber-500/25',
        'uas' => 'border-red-300 focus:border-red-500 focus:ring-red-500/25',
    ];
    
    $colorClass = $componentColors[$component] ?? $componentColors['tugas'];
@endphp

<div class="relative {{ $class }}">
    <input type="number"
           name="scores[{{ $studentId }}][{{ $component }}]"
           value="{{ $value }}"
           min="{{ $min }}"
           max="{{ $max }}"
           step="0.5"
           class="w-full text-center rounded-lg border border-outline-variant bg-surface-bright text-on-surface placeholder:text-on-surface-variant
                  px-3 py-2 text-body-md transition-colors
                  {{ $colorClass }}
                  {{ $disabled ? 'bg-surface-container-high cursor-not-allowed opacity-75' : 'hover:border-primary/50' }}
                  focus:outline-none focus:ring-2 focus:ring-offset-1"
           @if($disabled) disabled @endif
           autocomplete="off"
           inputmode="decimal">
    
    @if($value !== null && $value !== '')
        <div class="absolute bottom-1 right-1 text-[10px] font-medium {{ $value >= 75 ? 'text-green-600' : 'text-red-600' }}">
            {{ $value >= 75 ? '✓' : '✗' }}
        </div>
    @endif
</div>