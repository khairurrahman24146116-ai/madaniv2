@props([
    'schedule' => [],
    'variant' => 'default', // default, compact
    'showActions' => false,
    'class' => '',
])

@php
    $schedule = is_array($schedule) ? (object)$schedule : $schedule;
    $time = $schedule->time ?? ($schedule->start_time . ' - ' . $schedule->end_time);
    $subject = $schedule->subject ?? 'Mata Pelajaran';
    $class = $schedule->class ?? $schedule->classroom ?? 'Kelas';
    $type = $schedule->type ?? 'TEORI';
    $teacher = $schedule->teacher ?? '';
    $room = $schedule->room ?? '';
    
    $typeColors = [
        'TEORI' => 'bg-secondary-fixed text-on-secondary-fixed-variant',
        'PRAKTIKUM' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
        'UTS' => 'bg-warning-container text-on-warning-container',
        'UAS' => 'bg-error-container text-on-error-container',
        'REMEDIAL' => 'bg-primary-fixed text-on-primary-fixed-variant',
    ];
    
    $typeClass = $typeColors[$type] ?? 'bg-surface-container-high text-on-surface-variant';
@endphp

<x-card variant="default" padding="md" :border-left="'border-primary'" class="relative overflow-hidden hover:bg-surface-container-low transition-colors {{ $class }}">
    <div class="flex justify-between items-start mb-2">
        <span class="text-label-md text-primary font-bold">{{ $time }}</span>
        <span class="px-2 py-[2px] {{ $typeClass }} rounded text-[10px] font-bold">{{ $type }}</span>
    </div>
    <h4 class="text-headline-md text-on-surface mb-1">{{ $subject }}</h4>
    <p class="text-body-md text-on-surface-variant mb-4">{{ $class }}</p>
    
    @if($teacher || $room)
        <div class="flex flex-wrap gap-2 text-caption text-on-surface-variant">
            @if($teacher)
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">person</span>
                    {{ $teacher }}
                </span>
            @endif
            @if($room)
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">meeting_room</span>
                    {{ $room }}
                </span>
            @endif
        </div>
    @endif
    
    @if($showActions)
        <div class="mt-4 pt-4 border-t border-outline-variant flex gap-2">
            <x-button variant="ghost" size="sm" icon="visibility">Lihat</x-button>
            @if($showActions === 'teacher')
                <x-button variant="primary" size="sm" icon="how_to_reg" href="{{ route('attendances.form', ['schedule_id' => $schedule->id ?? '']) }}">Absensi</x-button>
            @endif
        </div>
    @endif
</x-card>