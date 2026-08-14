@extends('layouts.app')

@section('content')
<x-page-header 
    title="Surat & Pengumuman" 
    subtitle="Kelola surat sekolah"
    icon="description"
    :actions="[
        ['type' => 'button', 'label' => 'Buat Surat', 'icon' => 'add', 'variant' => 'primary', 'href' => route('admin.letters.create')],
    ]"
/>

<x-card variant="default">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Judul</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Tipe</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Status</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Dibuat</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($letters as $letter)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 text-body-md text-on-surface font-semibold">{{ $letter->title }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded text-label-md bg-surface-container-high text-on-surface-variant">{{ $letter->type }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($letter->is_published)
                            <span class="text-tertiary-container text-label-md">Terbit</span>
                        @else
                            <span class="text-warning text-label-md">Draf</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $letter->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.letters.edit', $letter) }}" class="text-primary hover:underline text-label-md">Edit</a>
                            <form action="{{ route('admin.letters.destroy', $letter) }}" method="POST"
                                data-confirm="Hapus surat ini?"
                                data-confirm-title="Hapus Surat"
                                data-confirm-variant="danger"
                                data-confirm-confirm-text="Ya, Hapus">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-error hover:underline text-label-md">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-on-surface-variant">Belum ada surat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($letters, 'links'))
    <div class="p-6 border-t border-outline-variant">{{ $letters->links() }}</div>
    @endif
</x-card>
@endsection
