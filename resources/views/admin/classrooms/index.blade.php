@extends('layouts.app')

@section('content')
<x-page-header title="Data Kelas" subtitle="Kelola kelas X, XI, XII" icon="meeting_room"
    :actions="[['label' => 'Tambah Kelas', 'icon' => 'add', 'href' => route('admin.classrooms.create')]]" />

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
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Kelas</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Tingkat</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Wali Kelas</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Tahun Ajaran</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-center">Siswa</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($classrooms as $c)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-lg py-md text-body-md text-on-surface font-semibold">{{ $c->name }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $c->grade }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface">{{ $c->waliKelas?->name ?? '-' }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $c->academic_year }}</td>
                    <td class="px-lg py-md text-center text-body-md text-on-surface-variant">{{ $c->students_count }}</td>
                    <td class="px-lg py-md text-right">
                        <a href="{{ route('admin.classrooms.edit', $c) }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80">
                            <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                        </a>
                        <form action="{{ route('admin.classrooms.destroy', $c) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kelas {{ $c->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 text-label-md text-error hover:text-error/80 ml-md">
                                <span class="material-symbols-outlined text-[18px]">delete</span> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-xl text-on-surface-variant">Belum ada kelas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($classrooms, 'links'))
    <div class="p-lg border-t border-outline-variant">{{ $classrooms->links() }}</div>
    @endif
</x-card>
@endsection
