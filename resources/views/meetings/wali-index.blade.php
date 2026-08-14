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

<x-card variant="default">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Subjek</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Tanggal Diminta</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Status</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-right">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($meetings as $meeting)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 text-body-md text-on-surface font-semibold">{{ $meeting->subject }}</td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $meeting->requested_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        @if($meeting->status === 'approved')
                            <span class="text-tertiary-container text-label-md bg-tertiary-fixed/40 px-2 py-0.5 rounded">Disetujui</span>
                        @elseif($meeting->status === 'rejected')
                            <span class="text-error text-label-md bg-error-container/50 px-2 py-0.5 rounded">Ditolak</span>
                        @else
                            <span class="text-warning text-label-md bg-warning-container/50 px-2 py-0.5 rounded">Menunggu</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="#"
                           class="text-primary hover:underline text-label-md"
                           onclick="showMeetingDetail(this); return false;"
                           data-subject="{{ $meeting->subject }}"
                           data-date="{{ $meeting->requested_date->format('d/m/Y') }}"
                           data-description="{{ $meeting->description }}"
                           data-status="{{ $meeting->status }}"
                           data-rejection="{{ $meeting->rejection_reason }}">Lihat</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-8 text-on-surface-variant">Belum ada permintaan pertemuan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($meetings, 'links'))
    <div class="p-6 border-t border-outline-variant">{{ $meetings->links() }}</div>
    @endif
</x-card>

<x-modal id="meeting-detail-modal" title="Detail Pertemuan">
    <div class="space-y-6">
        <div>
            <p class="text-caption text-on-surface-variant mb-1">SUBJEK</p>
            <p id="md-subject" class="text-body-md text-on-surface font-semibold"></p>
        </div>
        <div>
            <p class="text-caption text-on-surface-variant mb-1">TANGGAL DIMINTA</p>
            <p id="md-date" class="text-body-md text-on-surface"></p>
        </div>
        <div>
            <p class="text-caption text-on-surface-variant mb-1">DESKRIPSI</p>
            <p id="md-description" class="text-body-md text-on-surface leading-relaxed"></p>
        </div>
        <div>
            <p class="text-caption text-on-surface-variant mb-1">STATUS</p>
            <p id="md-status" class="text-body-md text-on-surface"></p>
        </div>
        <div id="md-rejection-wrap" class="hidden">
            <p class="text-caption text-on-surface-variant mb-1">ALASAN DITOLAK</p>
            <p id="md-rejection" class="text-body-md text-error"></p>
        </div>
    </div>
    <div class="mt-6 flex justify-end">
        <x-button variant="primary" type="button" icon="close" onclick="closeModal('meeting-detail-modal')">Tutup</x-button>
    </div>
</x-modal>

@push('scripts')
<script>
    function showMeetingDetail(link) {
        var labels = { approved: 'Disetujui', rejected: 'Ditolak', pending: 'Menunggu' };
        document.getElementById('md-subject').textContent = link.dataset.subject;
        document.getElementById('md-date').textContent = link.dataset.date;
        document.getElementById('md-description').textContent = link.dataset.description || '-';
        document.getElementById('md-status').textContent = labels[link.dataset.status] || link.dataset.status;
        var rejection = link.dataset.rejection;
        var wrap = document.getElementById('md-rejection-wrap');
        if (rejection) {
            document.getElementById('md-rejection').textContent = rejection;
            wrap.classList.remove('hidden');
        } else {
            wrap.classList.add('hidden');
        }
        openModal('meeting-detail-modal');
    }
</script>
@endpush
@endsection
