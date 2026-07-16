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
        'TEORI' => 'bg-blue-50 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300',
        'PRAKTIKUM' => 'bg-green-50 text-green-800 dark:bg-green-900/20 dark:text-green-300',
        'UTS' => 'bg-amber-50 text-amber-800 dark:bg-amber-900/20 dark:text-amber-300',
        'UAS' => 'bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300',
        'REMEDIAL' => 'bg-purple-50 text-purple-800 dark:bg-purple-900/20 dark:text-purple-300',
    ];
    
    $typeClass = $typeColors[$type] ?? 'bg-surface-container-high text-on-surface-variant';
@endphp

<x-card variant="default" padding="md" class="relative overflow-hidden hover:bg-surface-container-low transition-colors {{ $class }}" style="border-left: 4px solid var(--color-primary);">
    <div class="flex justify-between items-start mb-sm">
        <span class="text-label-md text-primary font-bold">{{ $time }}</span>
        <span class="px-sm py-[2px] {{ $typeClass }} rounded text-[10px] font-bold">{{ $type }}</span>
    </div>
    <h4 class="text-headline-md text-on-surface mb-xs">{{ $subject }}</h4>
    <p class="text-body-md text-on-surface-variant mb-md">{{ $class }}</p>
    
    @if($teacher || $room)
        <div class="flex flex-wrap gap-sm text-caption text-on-surface-variant">
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
        <div class="mt-md pt-md border-t border-outline-variant flex gap-sm">
            <x-button variant="ghost" size="sm" icon="visibility">Lihat</x-button>
            @if($showActions === 'teacher')
                <x-button variant="primary" size="sm" icon="how_to_reg" href="{{ route('attendances.form', ['schedule_id' => $schedule->id ?? '']) }}">Absensi</x-button>
            @endif
        </div>
    @endif
</x-card>