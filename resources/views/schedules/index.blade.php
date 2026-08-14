@extends('layouts.app')

@section('content')
<x-page-header 
    title="Jadwal Pelajaran" 
    subtitle="Jadwal mingguan blok sore (14:00-16:00)"
    icon="calendar_month"
    :actions="[
        ['type' => 'button', 'label' => 'Mobile View', 'icon' => 'phone_android', 'variant' => 'secondary', 'href' => route('schedules.mobile')],
    ]"
/>

<div class="bg-surface-container-low rounded-xl p-6 border border-outline-variant overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant">
                <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Hari</th>
                <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Waktu</th>
                <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Mata Pelajaran</th>
                <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Guru</th>
                <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Kelas</th>
                <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Ruang</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant">
            @forelse($scheduleGrid ?? [] as $s)
            <tr class="hover:bg-surface-container-low transition-colors">
                <td class="px-6 py-4 text-body-md text-on-surface">{{ ucfirst($s['day']) }}</td>
                <td class="px-6 py-4 text-body-md text-on-surface">{{ $s['start_time'] }} - {{ $s['end_time'] }}</td>
                <td class="px-6 py-4 text-body-md text-on-surface font-medium">{{ $s['subject'] }}</td>
                <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $s['teacher'] }}</td>
                <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $s['class'] ?? $s['teacher_short'] }}</td>
                <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $s['room'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-8 text-on-surface-variant">Belum ada jadwal.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection