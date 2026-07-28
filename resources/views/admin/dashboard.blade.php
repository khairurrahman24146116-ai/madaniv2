@extends('layouts.app')

@section('content')
<x-page-header title="Panel Admin" subtitle="Manajemen data master sistem" icon="admin_panel_settings" />

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-md mb-lg">
    <a href="{{ route('admin.classrooms.index') }}" class="block rounded-lg bg-surface-container-lowest border border-outline-variant p-lg hover:bg-surface-container-low transition-colors">
        <div class="flex items-center gap-md mb-sm">
            <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">meeting_room</span>
            </div>
            <div>
                <p class="text-headline-md font-semibold text-on-surface">Kelas</p>
                <p class="text-caption text-on-surface-variant">{{ \App\Models\Classroom::count() }} terdaftar</p>
            </div>
        </div>
    </a>
    <a href="{{ route('admin.subjects.index') }}" class="block rounded-lg bg-surface-container-lowest border border-outline-variant p-lg hover:bg-surface-container-low transition-colors">
        <div class="flex items-center gap-md mb-sm">
            <div class="w-12 h-12 bg-tertiary-container text-on-tertiary-container rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">book</span>
            </div>
            <div>
                <p class="text-headline-md font-semibold text-on-surface">Mapel</p>
                <p class="text-caption text-on-surface-variant">{{ \App\Models\Subject::count() }} terdaftar</p>
            </div>
        </div>
    </a>
    <a href="{{ route('admin.students.index') }}" class="block rounded-lg bg-surface-container-lowest border border-outline-variant p-lg hover:bg-surface-container-low transition-colors">
        <div class="flex items-center gap-md mb-sm">
            <div class="w-12 h-12 bg-secondary-container text-on-secondary-container rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">people</span>
            </div>
            <div>
                <p class="text-headline-md font-semibold text-on-surface">Siswa</p>
                <p class="text-caption text-on-surface-variant">{{ \App\Models\Student::count() }} terdaftar</p>
            </div>
        </div>
    </a>
    <a href="{{ route('admin.teacher-subjects.index') }}" class="block rounded-lg bg-surface-container-lowest border border-outline-variant p-lg hover:bg-surface-container-low transition-colors">
        <div class="flex items-center gap-md mb-sm">
            <div class="w-12 h-12 bg-error-container text-on-error-container rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">assignment_ind</span>
            </div>
            <div>
                <p class="text-headline-md font-semibold text-on-surface">Mapping</p>
                <p class="text-caption text-on-surface-variant">{{ \App\Models\TeacherSubject::count() }} pengajaran</p>
            </div>
        </div>
    </a>
</div>

{{-- Recent Activity Feed --}}
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl">
    <div class="flex items-center justify-between px-lg pt-lg pb-md border-b border-outline-variant">
        <div class="flex items-center gap-sm">
            <span class="material-symbols-outlined text-primary">timeline</span>
            <h3 class="text-headline-md font-semibold text-on-surface">Aktivitas Terkini</h3>
        </div>
        <a href="{{ route('admin.activity-logs.index') }}" class="text-label-md text-primary hover:underline">Lihat Semua</a>
    </div>
    <div class="divide-y divide-outline-variant">
        @php
            $recentLogs = \App\Models\ActivityLog::with('user')
                ->latest()
                ->take(10)
                ->get();
        @endphp
        @forelse($recentLogs as $log)
        <div class="flex items-start gap-md px-lg py-md hover:bg-surface-container-low transition-colors">
            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                @switch($log->action)
                    @case('login') bg-blue-100 text-blue-700 @break
                    @case('logout') bg-gray-100 text-gray-700 @break
                    @case('create') bg-green-100 text-green-700 @break
                    @case('update') bg-amber-100 text-amber-700 @break
                    @case('delete') bg-red-100 text-red-700 @break
                    @default bg-surface-container text-on-surface-variant
                @endswitch
            ">
                <span class="material-symbols-outlined text-[20px]">
                    @switch($log->action)
                        @case('login') login @break
                        @case('logout') logout @break
                        @case('create') add_circle @break
                        @case('update') edit @break
                        @case('delete') delete @break
                        @default circle
                    @endswitch
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-body-md text-on-surface">
                    <strong>{{ $log->user?->name ?? 'System' }}</strong>
                    <span class="text-on-surface-variant">
                        @switch($log->action)
                            @case('login') login @break
                            @case('logout') logout @break
                            @case('create') menambahkan @break
                            @case('update') mengubah @break
                            @case('delete') menghapus @break
                            @default {{ $log->action }}
                        @endswitch
                    </span>
                    <span class="text-on-surface-variant">{{ $log->description }}</span>
                </p>
                <div class="flex items-center gap-sm mt-1">
                    <span class="text-caption text-on-surface-variant">{{ $log->created_at->diffForHumans() }}</span>
                    @if($log->action === 'login')
                    <span class="text-caption text-on-surface-variant">&middot; {{ $log->ip_address ?? '-' }}</span>
                    @endif
                    <span class="text-caption text-on-surface-variant">&middot;
                        @switch($log->action)
                            @case('login') <span class="text-blue-600 font-medium">Login</span> @break
                            @case('logout') <span class="text-gray-600 font-medium">Logout</span> @break
                            @case('create') <span class="text-green-600 font-medium">Tambah</span> @break
                            @case('update') <span class="text-amber-600 font-medium">Ubah</span> @break
                            @case('delete') <span class="text-red-600 font-medium">Hapus</span> @break
                        @endswitch
                    </span>
                </div>
            </div>
            <span class="text-caption text-on-surface-variant tabular-nums shrink-0">{{ $log->created_at->format('H:i') }}</span>
        </div>
        @empty
        <div class="text-center py-xl text-on-surface-variant">
            <span class="material-symbols-outlined text-4xl mb-md block">timeline</span>
            <p>Belum ada aktivitas tercatat.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
