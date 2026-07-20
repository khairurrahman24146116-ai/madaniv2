@extends('layouts.app')

@section('content')
<x-page-header title="Data Jadwal" subtitle="Kelola jadwal mengajar sore" icon="calendar_month"
    :actions="[['label' => 'Tambah Jadwal', 'icon' => 'add', 'href' => route('admin.schedules.create')]]" />

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
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Hari</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Jam</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Guru</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Mapel</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Kelas</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-center">Jam Ke-</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($schedules as $s)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-lg py-md text-body-md text-on-surface font-semibold capitalize">{{ $s->day }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $s->start_time }} - {{ $s->end_time }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface">{{ $s->teacherSubject->user->name ?? '-' }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $s->teacherSubject->subject->name ?? '-' }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $s->teacherSubject->classroom->name ?? '-' }}</td>
                    <td class="px-lg py-md text-center text-body-md text-on-surface-variant">{{ $s->hour_order }}</td>
                    <td class="px-lg py-md text-right">
                        <a href="{{ route('admin.schedules.edit', $s) }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80">
                            <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                        </a>
                        <form action="{{ route('admin.schedules.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jadwal {{ $s->day }} jam {{ $s->start_time }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 text-label-md text-error hover:text-error/80 ml-md">
                                <span class="material-symbols-outlined text-[18px]">delete</span> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-xl text-on-surface-variant">Belum ada jadwal.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-md">
        {{ $schedules->links() }}
    </div>
</x-card>
@endsection
