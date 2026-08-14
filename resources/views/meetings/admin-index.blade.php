@extends('layouts.app')

@section('content')
<x-page-header 
    title="Permintaan Pertemuan" 
    subtitle="Kelola permintaan pertemuan dari wali murid"
    icon="event"
/>

<x-card variant="default">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Pemohon</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Subjek</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Tanggal</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Status</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($meetings as $meeting)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 text-body-md text-on-surface">{{ $meeting->user->name }}</td>
                    <td class="px-6 py-4 text-body-md text-on-surface font-semibold">{{ $meeting->subject }}</td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $meeting->requested_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        @if($meeting->status === 'approved')
                            <span class="text-tertiary-container text-label-md bg-tertiary-fixed/40 px-2 py-0.5 rounded">Disetujui</span>
                        @elseif($meeting->status === 'rejected')
                            <span class="text-error text-label-md bg-error-container/50 px-2 py-0.5 rounded">Ditolak</span>
                        @else
                            <span class="text-warning text-label-md bg-warning-container/50 px-2 py-0.5 rounded font-bold">Menunggu</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.meetings.show', $meeting) }}" class="text-primary hover:underline text-label-md">Proses</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-on-surface-variant">Belum ada permintaan pertemuan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($meetings, 'links'))
    <div class="p-6 border-t border-outline-variant">{{ $meetings->links() }}</div>
    @endif
</x-card>
@endsection
