@extends('layouts.app')

@section('content')
<x-page-header title="Data Siswa" subtitle="Kelola data siswa" icon="people"
    :actions="[['label' => 'Tambah Siswa', 'icon' => 'add', 'href' => route('admin.students.create')]]" />

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
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">NIS</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Nama</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Kelas</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-center">L/P</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-center">Aktif</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($students as $s)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-lg py-md"><span class="font-mono text-label-md">{{ $s->nis }}</span></td>
                    <td class="px-lg py-md text-body-md text-on-surface font-semibold">{{ $s->name }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $s->classroom->name ?? '-' }}</td>
                    <td class="px-lg py-md text-center text-body-md text-on-surface-variant">{{ $s->gender }}</td>
                    <td class="px-lg py-md text-center">
                        @if($s->is_active)
                        <span class="text-green-600 material-symbols-outlined text-[18px]">check_circle</span>
                        @else
                        <span class="text-on-surface-variant material-symbols-outlined text-[18px]">cancel</span>
                        @endif
                    </td>
                    <td class="px-lg py-md text-right">
                        <a href="{{ route('admin.students.edit', $s) }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80">
                            <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                        </a>
                        <form action="{{ route('admin.students.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('Hapus siswa {{ $s->name }}? Data terkait juga akan dihapus.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 text-label-md text-error hover:text-error/80 ml-md">
                                <span class="material-symbols-outlined text-[18px]">delete</span> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-xl text-on-surface-variant">Belum ada siswa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($students, 'links'))
    <div class="p-lg border-t border-outline-variant">{{ $students->links() }}</div>
    @endif
</x-card>
@endsection
