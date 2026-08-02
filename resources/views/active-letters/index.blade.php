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

@if(session('success'))
<div class="mb-lg p-md bg-green-50 text-green-800 rounded-xl text-[14px] flex items-start gap-3 border border-green-200">
    <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">check_circle</span>
    <div>{{ session('success') }}</div>
</div>
@endif

@if(session('error'))
<div class="mb-lg p-md bg-red-50 text-red-800 rounded-xl text-[14px] flex items-start gap-3 border border-red-200">
    <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">error</span>
    <div>{{ session('error') }}</div>
</div>
@endif

<x-card variant="default">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Siswa</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Kelas</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Pengaju</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Status</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($letters as $letter)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-lg py-md text-body-md text-on-surface font-semibold">{{ $letter->student->name }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $letter->student->classroom?->name ?? '-' }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $letter->teacher->name }}</td>
                    <td class="px-lg py-md">
                        @if($letter->status === 'selesai')
                            <span class="text-green-700 text-label-md bg-green-50 px-sm py-0.5 rounded">Selesai</span>
                        @elseif($letter->status === 'diambil')
                            <span class="text-blue-700 text-label-md bg-blue-50 px-sm py-0.5 rounded">Diambil</span>
                        @elseif($letter->status === 'ditolak')
                            <span class="text-red-700 text-label-md bg-red-50 px-sm py-0.5 rounded">Ditolak</span>
                        @endif
                    </td>
                    <td class="px-lg py-md text-right">
                        <div class="flex items-center justify-end gap-sm">
                            <a href="{{ route('active-letters.show', $letter) }}" class="text-primary hover:underline text-label-md">Detail</a>
                            @if($letter->status === 'selesai')
                                <form action="{{ route('active-letters.mark-taken', $letter) }}" method="POST" class="inline" onsubmit="return confirm('Tandai surat sudah diambil siswa?')">
                                    @csrf
                                    <button type="submit" class="text-blue-700 hover:underline text-label-md">Ambil</button>
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
                    <td colspan="5" class="text-center py-xl text-on-surface-variant">Belum ada pengajuan surat aktif.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($letters, 'links'))
    <div class="p-lg border-t border-outline-variant">{{ $letters->links() }}</div>
    @endif
</x-card>
@endsection
