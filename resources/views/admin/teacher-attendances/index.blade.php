@extends('layouts.app')

@section('content')
<x-page-header title="Absensi Guru" subtitle="Monitoring kehadiran guru" icon="badge" />

@if(session('success'))
<div class="mb-lg p-md bg-green-50 text-green-800 rounded-xl text-[14px] flex items-start gap-3 border border-green-200">
    <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">check_circle</span>
    <div>{{ session('success') }}</div>
</div>
@endif

{{-- Today's Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-md mb-lg">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg text-center">
        <p class="text-headline-xl text-on-surface font-bold">{{ $totalGuru }}</p>
        <p class="text-caption text-on-surface-variant mt-xs">Total Guru</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg text-center">
        <p class="text-headline-xl text-green-600 font-bold">{{ $hadir }}</p>
        <p class="text-caption text-on-surface-variant mt-xs">Hadir</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg text-center">
        <p class="text-headline-xl text-amber-600 font-bold">{{ $sakit }}</p>
        <p class="text-caption text-on-surface-variant mt-xs">Sakit</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg text-center">
        <p class="text-headline-xl text-blue-600 font-bold">{{ $izin }}</p>
        <p class="text-caption text-on-surface-variant mt-xs">Izin</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg text-center">
        <p class="text-headline-xl text-red-600 font-bold">{{ $alpa }}</p>
        <p class="text-caption text-on-surface-variant mt-xs">Alpa</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg text-center">
        <p class="text-headline-xl text-on-surface-variant font-bold">{{ $belumAbsen }}</p>
        <p class="text-caption text-on-surface-variant mt-xs">Belum Absen</p>
    </div>
</div>

{{-- Filters --}}
<form action="{{ route('admin.teacher-attendances.index') }}" method="GET" class="flex flex-wrap gap-md mb-lg p-md bg-surface-container-low rounded-xl border border-outline-variant">
    <div>
        <label class="text-label-md text-on-surface-variant block mb-xs">TANGGAL</label>
        <input type="date" name="date" value="{{ request('date', $today) }}" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md">
    </div>
    <div>
        <label class="text-label-md text-on-surface-variant block mb-xs">GURU</label>
        <select name="user_id" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md">
            <option value="">Semua Guru</option>
            @foreach($gurus as $guru)
            <option value="{{ $guru->id }}" @selected(request('user_id') == $guru->id)>{{ $guru->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-label-md text-on-surface-variant block mb-xs">STATUS</label>
        <select name="status" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md">
            <option value="">Semua</option>
            <option value="H" @selected(request('status') === 'H')>Hadir</option>
            <option value="S" @selected(request('status') === 'S')>Sakit</option>
            <option value="I" @selected(request('status') === 'I')>Izin</option>
            <option value="A" @selected(request('status') === 'A')>Alpa</option>
        </select>
    </div>
    <div class="self-end flex gap-sm">
        <x-button type="submit" variant="primary" icon="filter_list">Filter</x-button>
        <a href="{{ route('admin.teacher-attendances.index') }}" class="px-lg py-2 border border-outline-variant rounded-lg text-label-md text-on-surface-variant hover:bg-surface-container transition-colors inline-flex items-center">
            Reset
        </a>
    </div>
</form>

{{-- Table --}}
<x-card variant="default">
    <div class="p-lg border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
        <h3 class="text-headline-md text-on-surface">Data Absensi Guru</h3>
        <span class="text-caption text-on-surface-variant">{{ $attendances->total() }} records</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Tanggal</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Nama</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Mapel</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-center">Status</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Check-in</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Check-out</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($attendances as $a)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-lg py-md text-body-md text-on-surface">{{ $a->date }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface font-semibold">{{ $a->user->name }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $a->schedule?->teacherSubject?->subject?->name ?? '-' }}</td>
                    <td class="px-lg py-md text-center">
                        @php
                        $badge = ['H' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300', 'S' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-300', 'I' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300', 'A' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300'];
                        $label = ['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa'];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $badge[$a->status] ?? 'bg-surface-container text-on-surface-variant' }}">{{ $label[$a->status] ?? $a->status }}</span>
                    </td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">
                        @if($a->check_in)
                            {{ \Carbon\Carbon::parse($a->check_in)->format('H:i') }}
                        @else
                            <span class="text-on-surface-variant">-</span>
                        @endif
                    </td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">
                        @if($a->check_out)
                            {{ \Carbon\Carbon::parse($a->check_out)->format('H:i') }}
                        @else
                            <span class="text-on-surface-variant">-</span>
                        @endif
                    </td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $a->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-xl text-on-surface-variant">Belum ada data absensi guru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($attendances, 'links'))
    <div class="p-lg border-t border-outline-variant">
        {{ $attendances->links() }}
    </div>
    @endif
</x-card>
@endsection
