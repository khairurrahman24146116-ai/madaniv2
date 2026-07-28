@extends('layouts.app')

@section('content')
<x-page-header 
    title="Jadwal Pertemuan" 
    subtitle="Riwayat permintaan pertemuan dengan kepala sekolah"
    icon="event"
    :actions="[
        ['type' => 'button', 'label' => 'Minta Pertemuan', 'icon' => 'add', 'variant' => 'primary', 'href' => route('wali.meetings.create')],
    ]"
/>

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
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Subjek</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Tanggal Diminta</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Status</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-right">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($meetings as $meeting)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-lg py-md text-body-md text-on-surface font-semibold">{{ $meeting->subject }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $meeting->requested_date->format('d/m/Y') }}</td>
                    <td class="px-lg py-md">
                        @if($meeting->status === 'approved')
                            <span class="text-green-700 text-label-md bg-green-50 px-sm py-0.5 rounded">Disetujui</span>
                        @elseif($meeting->status === 'rejected')
                            <span class="text-red-700 text-label-md bg-red-50 px-sm py-0.5 rounded">Ditolak</span>
                        @else
                            <span class="text-amber-700 text-label-md bg-amber-50 px-sm py-0.5 rounded">Menunggu</span>
                        @endif
                    </td>
                    <td class="px-lg py-md text-right">
                        <a href="#" onclick="alert('Subjek: {{ $meeting->subject }}\nTanggal: {{ $meeting->requested_date->format('d/m/Y') }}\nDeskripsi: {{ $meeting->description }}\nStatus: {{ $meeting->status }}{{ $meeting->rejection_reason ? '\nAlasan: '.$meeting->rejection_reason : '' }}')" class="text-primary hover:underline text-label-md">Lihat</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-xl text-on-surface-variant">Belum ada permintaan pertemuan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($meetings, 'links'))
    <div class="p-lg border-t border-outline-variant">{{ $meetings->links() }}</div>
    @endif
</x-card>
@endsection
