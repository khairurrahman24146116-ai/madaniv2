@extends('layouts.app')

@section('content')
<x-page-header 
    title="Hubungi Kepala Sekolah" 
    subtitle="Kirim pesan ke kepala sekolah"
    icon="mail"
    :actions="[
        ['type' => 'button', 'label' => 'Pesan Baru', 'icon' => 'add', 'variant' => 'primary', 'href' => route('wali.contact.create')],
    ]"
/>

<x-card variant="default">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Subjek</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Status</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Tanggal</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($messages as $msg)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 text-body-md text-on-surface font-semibold">{{ $msg->subject }}</td>
                    <td class="px-6 py-4">
                        @if($msg->admin_reply)
                            <span class="text-tertiary-container text-label-md">Dibalas</span>
                        @else
                            <span class="text-warning text-label-md">Menunggu</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $msg->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="#" onclick="openModal('contact-detail-{{ $msg->id }}')" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80"><span class="material-symbols-outlined text-[18px]">visibility</span> Lihat</a>
                        <x-contact-detail :message="$msg" />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-8 text-on-surface-variant">Belum ada pesan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($messages, 'links'))
    <div class="p-6 border-t border-outline-variant">{{ $messages->links() }}</div>
    @endif
</x-card>
@endsection
