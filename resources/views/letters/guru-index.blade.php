@extends('layouts.app')

@section('content')
<x-page-header 
    title="Surat & Pengumuman" 
    subtitle="Daftar surat dari sekolah"
    icon="description"
/>

<x-card variant="default">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Judul</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Tipe</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Tanggal</th>
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
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $letter->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('guru.letters.show', $letter) }}" class="text-primary hover:underline text-label-md">Lihat</a>
                            <a href="{{ route('letters.print', $letter) }}" class="text-primary hover:underline text-label-md">Cetak</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-8 text-on-surface-variant">Belum ada surat.</td>
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
