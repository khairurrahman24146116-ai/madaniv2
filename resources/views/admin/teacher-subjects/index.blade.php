@extends('layouts.app')

@section('content')
<x-page-header title="Mapping Pengajaran" subtitle="Petakan guru ke mata pelajaran & kelas" icon="assignment_ind"
    :actions="[['label' => 'Tambah Mapping', 'icon' => 'add', 'href' => route('admin.teacher-subjects.create')]]" />

@if(session('success'))
<div class="mb-lg p-md bg-green-50 text-green-800 rounded-xl text-[14px] flex items-start gap-3 border border-green-200">
    <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">check_circle</span>
    <div>{{ session('success') }}</div>
</div>
@endif

<x-card variant="default">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Guru</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Mapel</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Kelas</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-center">Jadwal</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($mappings as $m)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-lg py-md text-body-md text-on-surface font-semibold">{{ $m->user->name }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $m->subject->name }} ({{ $m->subject->code }})</td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $m->classroom->name }}</td>
                    <td class="px-lg py-md text-center text-body-md text-on-surface-variant">{{ $m->schedules->count() }}</td>
                    <td class="px-lg py-md text-right">
                        <form action="{{ route('admin.teacher-subjects.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('Hapus mapping {{ $m->user->name }} - {{ $m->subject->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 text-label-md text-error hover:text-error/80">
                                <span class="material-symbols-outlined text-[18px]">delete</span> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-xl text-on-surface-variant">Belum ada mapping pengajaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endsection
