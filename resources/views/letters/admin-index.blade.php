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
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Judul</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Tipe</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Status</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Dibuat</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($letters as $letter)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-lg py-md text-body-md text-on-surface font-semibold">{{ $letter->title }}</td>
                    <td class="px-lg py-md">
                        <span class="px-sm py-0.5 rounded text-label-md bg-surface-container-high text-on-surface-variant">{{ $letter->type }}</span>
                    </td>
                    <td class="px-lg py-md">
                        @if($letter->is_published)
                            <span class="text-green-700 text-label-md">Terbit</span>
                        @else
                            <span class="text-amber-700 text-label-md">Draf</span>
                        @endif
                    </td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $letter->created_at->format('d/m/Y') }}</td>
                    <td class="px-lg py-md text-right">
                        <div class="flex items-center justify-end gap-sm">
                            <a href="{{ route('admin.letters.edit', $letter) }}" class="text-primary hover:underline text-label-md">Edit</a>
                            <form action="{{ route('admin.letters.destroy', $letter) }}" method="POST" onsubmit="return confirm('Hapus surat ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-error hover:underline text-label-md">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-xl text-on-surface-variant">Belum ada surat.</td>
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
