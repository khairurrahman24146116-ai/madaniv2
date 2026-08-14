@extends('layouts.app')

@section('content')
@php
    $totalStudents = \App\Models\Student::where('is_active', true)->count();
    $totalTeachers = \App\Models\User::where('role', 'guru')->count();
    $activeSessions = \Laravel\Sanctum\PersonalAccessToken::where('last_used_at', '>=', now()->subMinutes(30))->count();
    $pendingRequests = \App\Models\ActiveLetterRequest::where('status', 'progres')
        ->orWhere('taken_at', null)
        ->latest()->take(5)->get();
    $recentLogs = \App\Models\ActivityLog::with('user')->latest()->take(5)->get();
@endphp

{{-- Desktop Header --}}
<div class="hidden md:flex justify-between items-center mb-8">
    <div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Admin Dashboard</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Ringkasan aktivitas dan data sistem hari ini.</p>
    </div>
    <div class="flex items-center gap-4">
        <span class="bg-surface-container-high px-4 py-2 rounded-lg flex items-center gap-2 font-label-mono text-label-mono hover:bg-surface-variant transition-colors border border-outline-variant">
            Role: <span class="font-bold text-primary">{{ auth()->user()->name }}</span>
        </span>
        <div class="flex items-center gap-2 text-on-surface-variant">
            <a href="{{ route('profile.edit') }}" class="p-2 hover:bg-surface-variant/50 rounded-full transition-colors">
                <span class="material-symbols-outlined">settings</span>
            </a>
            <img alt="Foto profil"
                 class="w-10 h-10 rounded-full object-cover ml-2 border-2 border-surface-container-high"
                 src="{{ auth()->user()->profile_photo_path ? asset('storage/'.auth()->user()->profile_photo_path) : 'https://ui.shadcn.com/placeholder.svg' }}" />
        </div>
    </div>
</div>

{{-- Mobile Header --}}
<div class="md:hidden flex items-center justify-between gap-3 mb-6">
    <div class="min-w-0">
        <h1 class="font-headline-lg text-headline-lg text-primary leading-tight">Admin Dashboard</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Ringkasan aktivitas hari ini.</p>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <a href="{{ route('profile.edit') }}" class="p-2 hover:bg-surface-variant/50 rounded-full transition-colors text-on-surface-variant">
            <span class="material-symbols-outlined">settings</span>
        </a>
        <img alt="Foto profil"
             class="w-10 h-10 rounded-full object-cover border-2 border-surface-container-high"
             src="{{ auth()->user()->profile_photo_path ? asset('storage/'.auth()->user()->profile_photo_path) : 'https://ui.shadcn.com/placeholder.svg' }}" />
    </div>
</div>

{{-- Key Metrics Bento Grid --}}
<section class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-gutter">
    <div class="reveal stagger-delay bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm flex flex-col relative overflow-hidden group lift" style="--stagger: 0">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-secondary-container/20 rounded-full blur-xl group-hover:bg-secondary-container/40 transition-colors"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div class="p-3 bg-surface-container-high rounded-lg text-primary">
                <span class="material-symbols-outlined animate-pop">groups</span>
            </div>
        </div>
        <h3 class="font-body-lg text-on-surface-variant mb-1 relative z-10">Total Siswa Aktif</h3>
        <div class="font-data-table text-4xl text-primary font-bold relative z-10"><span class="count-up" data-count="{{ number_format($totalStudents) }}">0</span></div>
    </div>

    <div class="reveal stagger-delay bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm flex flex-col relative overflow-hidden group lift" style="--stagger: 1">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary-container/20 rounded-full blur-xl group-hover:bg-primary-container/40 transition-colors"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div class="p-3 bg-surface-container-high rounded-lg text-primary">
                <span class="material-symbols-outlined animate-pop">badge</span>
            </div>
        </div>
        <h3 class="font-body-lg text-on-surface-variant mb-1 relative z-10">Total Guru &amp; Staf</h3>
        <div class="font-data-table text-4xl text-primary font-bold relative z-10"><span class="count-up" data-count="{{ number_format($totalTeachers) }}">0</span></div>
    </div>

    <div class="reveal stagger-delay bg-primary-container text-on-primary-container rounded-xl p-6 shadow-sm flex flex-col relative overflow-hidden" style="--stagger: 2">
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 100% 0%, #d9e2ff 0%, transparent 50%);"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div class="p-3 bg-primary/30 rounded-lg text-on-primary-container">
                <span class="material-symbols-outlined animate-pop">devices</span>
            </div>
            <span class="flex h-3 w-3 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary-fixed opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-secondary-fixed-dim"></span>
            </span>
        </div>
        <h3 class="font-body-lg text-on-primary-container/80 mb-1 relative z-10">Sesi Login Aktif</h3>
        <div class="font-data-table text-4xl text-primary-fixed font-bold relative z-10"><span class="count-up" data-count="{{ number_format($activeSessions) }}">0</span></div>
    </div>
</section>

{{-- Main Data Layout --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">

    {{-- Left Column --}}
    <div class="lg:col-span-2 flex flex-col gap-gutter">

        {{-- Recent User Activity --}}
        <section class="reveal stagger-delay bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col" style="--stagger: 0">
            <div class="p-4 border-b border-outline-variant bg-surface-container-low flex justify-between items-center">
                <h2 class="font-title-lg text-primary">Aktivitas Pengguna Terakhir</h2>
                <a href="{{ route('admin.activity-logs.index') }}" class="text-secondary font-label-mono text-label-mono hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-lowest border-b border-outline-variant">
                            <th class="p-4 font-label-mono text-label-mono text-on-surface-variant font-semibold">User</th>
                            <th class="p-4 font-label-mono text-label-mono text-on-surface-variant font-semibold">Tindakan</th>
                            <th class="p-4 font-label-mono text-label-mono text-on-surface-variant font-semibold text-right">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="font-data-table">
                        @forelse($recentLogs as $log)
                            @php
                                $actionIcons = [
                                    'login' => 'login', 'logout' => 'logout',
                                    'create' => 'add_circle', 'update' => 'edit', 'delete' => 'delete',
                                ];
                            @endphp
                            <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                                <td class="p-4 font-body-md text-body-md text-on-background">{{ $log->user?->name ?? 'System' }}</td>
                                <td class="p-4">
                                    <span class="bg-surface-container-high px-2 py-1 rounded text-xs inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">{{ $actionIcons[$log->action] ?? 'circle' }}</span>
                                        {{ ucfirst($log->action) }}
                                    </span>
                                    <span class="text-on-surface-variant ml-1">{{ $log->description }}</span>
                                </td>
                                <td class="p-4 text-right text-on-surface-variant">{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-4 text-center text-on-surface-variant">Belum ada aktivitas tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Pending Document Requests --}}
        <section class="reveal stagger-delay bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col" style="--stagger: 1">
            <div class="p-4 border-b border-outline-variant bg-surface-container-low flex justify-between items-center">
                <h2 class="font-title-lg text-primary">Permintaan Dokumen Tertunda</h2>
                <span class="bg-error-container text-on-error-container font-data-table px-2 py-1 rounded-full text-xs">{{ $pendingRequests->count() }} Perlu Aksi</span>
            </div>
            <div class="p-4 flex flex-col gap-3">
                @forelse($pendingRequests as $req)
                    <div class="flex items-center justify-between gap-2 p-3 border border-outline-variant rounded-lg hover:border-primary transition-colors bg-surface">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="bg-secondary-container/30 p-2 rounded-lg text-secondary flex-shrink-0">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="min-w-0">
                                <div class="font-body-md text-body-md font-semibold text-on-background truncate">{{ $req->type === 'surat_aktif' ? 'Surat Keterangan Aktif' : 'Surat '.ucfirst($req->type) }}</div>
                                <div class="font-data-table text-on-surface-variant text-xs">NIS: {{ $req->student->nis ?? '-' }}</div>
                            </div>
                        </div>
                        <a href="{{ route('active-letters.show', $req) }}"
                           class="p-2 text-secondary hover:bg-secondary-container/30 rounded-lg transition-colors flex-shrink-0">
                            <span class="material-symbols-outlined text-sm">open_in_new</span>
                        </a>
                    </div>
                @empty
                    <div class="p-4 text-center text-on-surface-variant font-body-md text-body-md">
                        Tidak ada permintaan dokumen tertunda.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    {{-- Right Column: Master Data --}}
    <div class="flex flex-col gap-gutter">
        <section class="reveal stagger-delay bg-surface-container-low border border-outline-variant rounded-xl shadow-sm p-6" style="--stagger: 2">
            <h2 class="font-title-lg text-primary mb-4">Master Data</h2>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.classrooms.index') }}" class="flex flex-col items-center justify-center p-4 bg-surface-container-lowest border border-outline-variant rounded-lg hover:bg-primary-container hover:text-on-primary-container hover:border-primary transition-all group">
                    <span class="material-symbols-outlined mb-2 text-secondary group-hover:text-on-primary-container">meeting_room</span>
                    <span class="font-label-mono text-label-mono">Data Kelas</span>
                </a>
                <a href="{{ route('admin.students.index') }}" class="flex flex-col items-center justify-center p-4 bg-surface-container-lowest border border-outline-variant rounded-lg hover:bg-primary-container hover:text-on-primary-container hover:border-primary transition-all group">
                    <span class="material-symbols-outlined mb-2 text-secondary group-hover:text-on-primary-container">face</span>
                    <span class="font-label-mono text-label-mono">Data Siswa</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center justify-center p-4 bg-surface-container-lowest border border-outline-variant rounded-lg hover:bg-primary-container hover:text-on-primary-container hover:border-primary transition-all group">
                    <span class="material-symbols-outlined mb-2 text-secondary group-hover:text-on-primary-container">person_apron</span>
                    <span class="font-label-mono text-label-mono">Data Guru</span>
                </a>
                <a href="{{ route('admin.subjects.index') }}" class="flex flex-col items-center justify-center p-4 bg-surface-container-lowest border border-outline-variant rounded-lg hover:bg-primary-container hover:text-on-primary-container hover:border-primary transition-all group">
                    <span class="material-symbols-outlined mb-2 text-secondary group-hover:text-on-primary-container">menu_book</span>
                    <span class="font-label-mono text-label-mono">Data Mapel</span>
                </a>
            </div>
        </section>
    </div>
</div>
@endsection
