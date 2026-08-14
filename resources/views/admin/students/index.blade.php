@extends('layouts.app')

@section('content')
<x-page-header title="Data Siswa" subtitle="Kelola data siswa" icon="people"
    :actions="[
        ['label' => 'Tambah Siswa', 'icon' => 'add', 'href' => route('admin.students.create')],
        ['label' => 'Import Excel', 'icon' => 'upload_file', 'variant' => 'secondary', 'href' => route('admin.students.import-form')],
    ]" />

<form method="GET" action="{{ route('admin.students.index') }}" class="mb-6">
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant material-symbols-outlined text-[18px]">search</span>
            <input type="text" name="search" placeholder="Cari nama atau NIS..." value="{{ request('search') }}"
                   class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface pl-9 pr-4 py-2.5 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
        </div>
        <select name="status" class="rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-2.5 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors min-w-[140px]">
            <option value="">Semua Status</option>
            <option value="1" @selected(request('status') === '1')>Aktif</option>
            <option value="0" @selected(request('status') === '0')>Tidak Aktif</option>
        </select>
        <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary text-on-primary text-label-md font-semibold hover:bg-primary/90 transition-colors">Cari</button>
        @if(request('search') || request('status') !== null)
            <a href="{{ route('admin.students.index') }}" class="px-6 py-2.5 rounded-lg border border-outline-variant text-label-md font-medium text-on-surface hover:bg-surface-container-high transition-colors text-center">Reset</a>
        @endif
    </div>
</form>

<x-card variant="default">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">NIS</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Nama</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Kelas</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-center">L/P</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-center">Status</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($students as $s)
                <tr class="hover:bg-surface-container-low transition-colors {{ !$s->is_active ? 'opacity-60' : '' }}">
                    <td class="px-6 py-4"><span class="font-mono text-label-md">{{ $s->nis }}</span></td>
                    <td class="px-6 py-4 text-body-md text-on-surface font-semibold">{{ $s->name }}</td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $s->classroom->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-center text-body-md text-on-surface-variant">{{ $s->gender }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($s->is_active)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-tertiary-fixed/40 text-tertiary-container text-label-md font-semibold border border-tertiary/30">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-error-container/50 text-error text-label-md font-semibold border border-error/30">
                                <span class="material-symbols-outlined text-[14px]">cancel</span>
                                Tidak Aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <a href="{{ route('admin.students.edit', $s) }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80">
                            <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                        </a>
                        <a href="{{ route('admin.students.move-form', $s) }}" class="inline-flex items-center gap-1 text-label-md text-secondary hover:text-secondary/80 ml-4">
                            <span class="material-symbols-outlined text-[18px]">swap_horiz</span> Pindah
                        </a>
                        <form action="{{ route('admin.students.destroy', $s) }}" method="POST" class="inline"
                            data-confirm="Hapus siswa {{ $s->name }}? Data terkait juga akan dihapus."
                            data-confirm-title="Hapus Siswa"
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
                <tr><td colspan="6" class="text-center py-8 text-on-surface-variant">
                    @if(request('search') || request('status') !== null)
                        Tidak ada siswa yang cocok dengan pencarian.
                    @else
                        Belum ada siswa.
                    @endif
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($students, 'links'))
    <div class="p-6 border-t border-outline-variant">{{ $students->links() }}</div>
    @endif
</x-card>
@endsection
