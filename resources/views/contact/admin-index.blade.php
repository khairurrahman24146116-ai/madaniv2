@extends('layouts.app')

@section('content')
<x-page-header 
    title="Pesan Masuk" 
    subtitle="Pesan dari wali murid"
    icon="mail"
/>

<x-card variant="default">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Pengirim</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Subjek</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Status</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Tanggal</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($messages as $msg)
                <tr class="hover:bg-surface-container-low transition-colors {{ !$msg->is_read ? 'font-semibold' : '' }}">
                    <td class="px-6 py-4 text-body-md text-on-surface">{{ $msg->user->name }}</td>
                    <td class="px-6 py-4 text-body-md text-on-surface">{{ $msg->subject }}</td>
                    <td class="px-6 py-4">
                        @if($msg->admin_reply)
                            <span class="text-tertiary-container text-label-md">Dibalas</span>
                        @else
                            <span class="text-warning text-label-md">Baru</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $msg->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.contact.show', $msg) }}" class="text-primary hover:underline text-label-md">Lihat & Balas</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-on-surface-variant">Belum ada pesan masuk.</td>
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
