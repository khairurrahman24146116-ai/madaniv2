@extends('layouts.app')

@section('content')
<x-page-header title="Absensi Guru" subtitle="Monitoring kehadiran guru" icon="badge" />

{{-- Today's Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 text-center">
        <p class="text-headline-xl text-on-surface font-bold">{{ $totalGuru }}</p>
        <p class="text-caption text-on-surface-variant mt-1">Total Guru</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 text-center">
        <p class="text-headline-xl text-tertiary-container font-bold">{{ $hadir }}</p>
        <p class="text-caption text-on-surface-variant mt-1">Hadir</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 text-center">
        <p class="text-headline-xl text-warning font-bold">{{ $sakit }}</p>
        <p class="text-caption text-on-surface-variant mt-1">Sakit</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 text-center">
        <p class="text-headline-xl text-secondary font-bold">{{ $izin }}</p>
        <p class="text-caption text-on-surface-variant mt-1">Izin</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 text-center">
        <p class="text-headline-xl text-error font-bold">{{ $alpa }}</p>
        <p class="text-caption text-on-surface-variant mt-1">Alpa</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 text-center">
        <p class="text-headline-xl text-on-surface-variant font-bold">{{ $belumAbsen }}</p>
        <p class="text-caption text-on-surface-variant mt-1">Belum Absen</p>
    </div>
</div>

{{-- Filters --}}
<form action="{{ route('admin.teacher-attendances.index') }}" method="GET" class="flex flex-wrap gap-4 mb-6 p-4 bg-surface-container-low rounded-xl border border-outline-variant">
    <div>
        <label class="text-label-md text-on-surface-variant block mb-1">TANGGAL</label>
        <input type="date" name="date" value="{{ request('date', $today) }}" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md">
    </div>
    <div>
        <label class="text-label-md text-on-surface-variant block mb-1">GURU</label>
        <select name="user_id" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md">
            <option value="">Semua Guru</option>
            @foreach($gurus as $guru)
            <option value="{{ $guru->id }}" @selected(request('user_id') == $guru->id)>{{ $guru->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-label-md text-on-surface-variant block mb-1">STATUS</label>
        <select name="status" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md">
            <option value="">Semua</option>
            <option value="H" @selected(request('status') === 'H')>Hadir</option>
            <option value="S" @selected(request('status') === 'S')>Sakit</option>
            <option value="I" @selected(request('status') === 'I')>Izin</option>
            <option value="A" @selected(request('status') === 'A')>Alpa</option>
        </select>
    </div>
    <div class="self-end flex gap-2">
        <x-button type="submit" variant="primary" icon="filter_list">Filter</x-button>
        <a href="{{ route('admin.teacher-attendances.index') }}" class="px-6 py-2 border border-outline-variant rounded-lg text-label-md text-on-surface-variant hover:bg-surface-container transition-colors inline-flex items-center">
            Reset
        </a>
    </div>
</form>

{{-- Table --}}
<x-card variant="default">
    <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
        <h3 class="text-headline-md text-on-surface">Data Absensi Guru</h3>
        <span class="text-caption text-on-surface-variant">{{ $attendances->total() }} records</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Tanggal</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Nama</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Mapel</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-center">Status</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Check-in</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Check-out</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($attendances as $a)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 text-body-md text-on-surface">{{ $a->date }}</td>
                    <td class="px-6 py-4 text-body-md text-on-surface font-semibold">{{ $a->user->name }}</td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $a->schedule?->teacherSubject?->subject?->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        @php
                        $badge = ['H' => 'bg-tertiary-fixed/40 text-on-tertiary-fixed-variant  ', 'S' => 'bg-warning-container/60 text-on-warning-container  ', 'I' => 'bg-secondary-fixed/60 text-on-secondary-fixed-variant  ', 'A' => 'bg-error-container/60 text-on-error-container  '];
                        $label = ['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa'];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $badge[$a->status] ?? 'bg-surface-container text-on-surface-variant' }}">{{ $label[$a->status] ?? $a->status }}</span>
                    </td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">
                        @if($a->check_in)
                            {{ \Carbon\Carbon::parse($a->check_in)->format('H:i') }}
                        @else
                            <span class="text-on-surface-variant">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">
                        @if($a->check_out)
                            {{ \Carbon\Carbon::parse($a->check_out)->format('H:i') }}
                        @else
                            <span class="text-on-surface-variant">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $a->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-on-surface-variant">Belum ada data absensi guru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($attendances, 'links'))
    <div class="p-6 border-t border-outline-variant">
        {{ $attendances->links() }}
    </div>
    @endif
</x-card>
@endsection
