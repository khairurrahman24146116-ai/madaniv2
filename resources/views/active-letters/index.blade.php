@extends('layouts.app')

@section('content')
<x-page-header 
    title="Surat Aktif Siswa" 
    subtitle="Pengajuan surat keterangan aktif siswa"
    icon="badge"
    :actions="[
        ['type' => 'button', 'label' => 'Ajukan Surat', 'icon' => 'add', 'variant' => 'primary', 'href' => route('active-letters.create')],
    ]"
/>

<x-card variant="default">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Siswa</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Kelas</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Pengaju</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Status</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($letters as $letter)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 text-body-md text-on-surface font-semibold">{{ $letter->student->name }}</td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $letter->student->classroom?->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $letter->teacher->name }}</td>
                    <td class="px-6 py-4">
                        @if($letter->status === 'selesai')
                            <span class="text-tertiary-container text-label-md bg-tertiary-fixed/40 px-2 py-0.5 rounded">Selesai</span>
                        @elseif($letter->status === 'diambil')
                            <span class="text-secondary text-label-md bg-secondary-fixed/40 px-2 py-0.5 rounded">Diambil</span>
                        @elseif($letter->status === 'ditolak')
                            <span class="text-error text-label-md bg-error-container/50 px-2 py-0.5 rounded">Ditolak</span>
                        @else
                            <span class="text-warning text-label-md bg-warning-container/50 px-2 py-0.5 rounded">Dalam Proses</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('active-letters.show', $letter) }}" class="text-primary hover:underline text-label-md">Detail</a>
                            @if($letter->status === 'selesai')
                                <form action="{{ route('active-letters.mark-taken', $letter) }}" method="POST" class="inline"
                                    data-confirm="Tandai surat sudah diambil siswa?"
                                    data-confirm-title="Tandai Diambil"
                                    data-confirm-variant="info"
                                    data-confirm-confirm-text="Ya, Tandai">
                                    @csrf
                                    <button type="submit" class="text-secondary hover:underline text-label-md">Ambil</button>
                                </form>
                                <a href="{{ route('active-letters.print', $letter) }}" class="text-primary hover:underline text-label-md">Cetak</a>
                            @endif
                            @if($letter->status === 'diambil')
                                <a href="{{ route('active-letters.print', $letter) }}" class="text-primary hover:underline text-label-md">Cetak</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-on-surface-variant">Belum ada pengajuan surat aktif.</td>
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
