@extends('layouts.app')

@section('content')
<x-page-header 
    title="Jadwal Hari Ini" 
    subtitle="Tampilan mobile untuk guru"
    icon="calendar_month"
    :actions="[
        ['type' => 'button', 'label' => 'Desktop', 'icon' => 'desktop_mac', 'variant' => 'secondary', 'href' => route('schedules.index')],
    ]"
/>

<div class="flex flex-wrap gap-sm mb-lg">
    @foreach(['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'] as $d)
        <a href="{{ route('schedules.mobile', ['day' => $d]) }}" class="px-4 py-2 rounded-lg text-label-md font-medium transition-colors @if($d === $currentDay) bg-primary text-on-primary @else bg-surface-container-high text-on-surface-variant @endif">
            {{ ucfirst($d) }}
        </a>
    @endforeach
</div>

<div class="space-y-md">
    @forelse($schedules ?? [] as $schedule)
        <x-schedule-card :schedule="$schedule" variant="mobile" />
    @empty
        <x-card variant="default" padding="md" class="text-center">
            <h4 class="text-headline-md text-on-surface mb-xs">Tidak ada jadwal pada hari ini</h4>
            <p class="text-body-md text-on-surface-variant">Jadwal sesi besok akan muncul di sini.</p>
        </x-card>
    @endforelse
</div>
@endsection