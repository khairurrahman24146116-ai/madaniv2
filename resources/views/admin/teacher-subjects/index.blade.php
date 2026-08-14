@extends('layouts.app')

@section('content')
<x-page-header title="Mapping Pengajaran" subtitle="Petakan guru ke mata pelajaran & kelas" icon="assignment_ind"
    :actions="[['label' => 'Tambah Mapping', 'icon' => 'add', 'href' => route('admin.teacher-subjects.create')]]" />

<x-card variant="default">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Guru</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Mapel</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Kelas</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-center">Jadwal</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($mappings as $m)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 text-body-md text-on-surface font-semibold">{{ $m->user->name }}</td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $m->subject->name }} ({{ $m->subject->code }})</td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $m->classroom->name }}</td>
                    <td class="px-6 py-4 text-center text-body-md text-on-surface-variant">{{ $m->schedules_count }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.teacher-subjects.edit', $m) }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80">
                            <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                        </a>
                        <form action="{{ route('admin.teacher-subjects.destroy', $m) }}" method="POST" class="inline"
                            data-confirm="Hapus mapping {{ $m->user->name }} - {{ $m->subject->name }}?"
                            data-confirm-title="Hapus Mapping"
                            data-confirm-variant="danger"
                            data-confirm-confirm-text="Ya, Hapus">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 text-label-md text-error hover:text-error/80 ml-4">
                                <span class="material-symbols-outlined text-[18px]">delete</span> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-8 text-on-surface-variant">Belum ada mapping pengajaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($mappings, 'links'))
    <div class="p-6 border-t border-outline-variant">{{ $mappings->links() }}</div>
    @endif
</x-card>
@endsection
