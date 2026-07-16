@extends('layouts.app')

@section('content')
<x-page-header title="Data Mata Pelajaran" subtitle="Kelola mata pelajaran" icon="book"
    :actions="[['label' => 'Tambah Mapel', 'icon' => 'add', 'href' => route('admin.subjects.create')]]" />

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
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Kode</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Nama</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-center">Pengajar</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($subjects as $s)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-lg py-md"><span class="font-mono text-label-md bg-surface-container px-2 py-0.5 rounded border border-outline-variant">{{ $s->code }}</span></td>
                    <td class="px-lg py-md text-body-md text-on-surface font-semibold">{{ $s->name }}</td>
                    <td class="px-lg py-md text-center text-body-md text-on-surface-variant">{{ $s->teacher_subjects_count }}</td>
                    <td class="px-lg py-md text-right">
                        <a href="{{ route('admin.subjects.edit', $s) }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80">
                            <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                        </a>
                        <form action="{{ route('admin.subjects.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('Hapus mapel {{ $s->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 text-label-md text-error hover:text-error/80 ml-md">
                                <span class="material-symbols-outlined text-[18px]">delete</span> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-xl text-on-surface-variant">Belum ada mata pelajaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endsection
