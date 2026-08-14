@extends('layouts.app')

@section('content')
<x-page-header title="Bobot Nilai" subtitle="Konfigurasi persentase bobot penilaian per mapel" icon="tune"
    :actions="[['label' => 'Tambah Bobot', 'icon' => 'add', 'href' => route('admin.score-components.create')]]" />

<x-card variant="default">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Mapel</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Komponen</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Bobot</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Semester</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Tahun Ajaran</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($components as $c)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 text-body-md text-on-surface font-semibold">{{ $c->subject->name }}</td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $c->name }} ({{ $c->code }})</td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $c->weight }}%</td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant capitalize">{{ $c->semester }}</td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $c->academic_year }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.score-components.edit', $c) }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80">
                            <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                        </a>
                        <form action="{{ route('admin.score-components.destroy', $c) }}" method="POST" class="inline"
                            data-confirm="Hapus bobot {{ $c->name }}?"
                            data-confirm-title="Hapus Bobot"
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
                <tr><td colspan="6" class="text-center py-8 text-on-surface-variant">Belum ada konfigurasi bobot nilai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($components, 'links'))
    <div class="p-6 border-t border-outline-variant">{{ $components->links() }}</div>
    @endif
</x-card>
@endsection
