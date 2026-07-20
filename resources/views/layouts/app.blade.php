<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Madani-SMS @isset($title) - {{ $title }} @endisset</title>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400..700&family=Inter:wght@100..900&family=JetBrains+Mono:wght@400;500;600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&family=Source+Serif+4:opsz,wght@8..60,400;8..60,600;8..60,700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="bg-background text-on-surface font-sans antialiased min-h-screen">
    @auth
    {{-- TopAppBar --}}
    <header class="w-full top-0 sticky bg-surface border-b border-outline-variant z-50">
        <div class="flex justify-between items-center px-margin-mobile md:px-margin-desktop py-sm max-w-[1440px] mx-auto">
            <div class="flex items-center gap-md">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-sm">
                    <span class="w-9 h-9 bg-primary text-on-primary rounded-md flex items-center justify-center material-symbols-outlined">school</span>
                    <div>
                        <h1 class="text-headline-lg-mobile md:text-headline-lg font-bold text-on-surface leading-none">Madani-SMS</h1>
                        <p class="hidden sm:block text-caption text-on-surface-variant mt-1">SMA Sore Dayah Madani</p>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-md">
                <span class="text-label-md text-on-surface-variant hidden md:block">{{ auth()->user()->name }}</span>
                <button id="dark-toggle" class="p-sm hover:bg-surface-container-high rounded-md transition-colors" title="Ubah tema" aria-label="Ubah tema">
                    <span class="material-symbols-outlined text-on-surface-variant" id="theme-icon">dark_mode</span>
                </button>
                <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container font-bold border border-outline-variant overflow-hidden">
                    <span>{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
            </div>
        </div>
    </header>

    {{-- SideNavBar (desktop) --}}
    <aside class="hidden lg:flex h-[calc(100vh-64px)] w-64 flex-col bg-surface-container-low border-r border-outline-variant fixed left-0 top-16 z-40 overflow-y-auto">
        <nav class="flex-1 space-y-xs py-md">
            @if(auth()->user()->isWaliMurid())
            <x-nav-item href="{{ route('wali-murid.dashboard') }}" icon="dashboard" :active="request()->routeIs('wali-murid.*')">Dashboard</x-nav-item>
            @else
            <x-nav-item href="{{ route('dashboard') }}" icon="dashboard" :active="request()->routeIs('dashboard')">Dashboard</x-nav-item>
            <x-nav-item href="{{ route('attendances.form') }}" icon="how_to_reg" :active="request()->routeIs('attendances.*')">Absensi</x-nav-item>
            <x-nav-item href="{{ route('schedules.index') }}" icon="calendar_month" :active="request()->routeIs('schedules.*')">Jadwal</x-nav-item>
            <x-nav-item href="{{ route('scores.create') }}" icon="grade" :active="request()->routeIs('scores.*')">Nilai</x-nav-item>
            <x-nav-item href="{{ route('scores.rapor-preview') }}" icon="assignment" :active="request()->routeIs('scores.rapor-preview')">E-Rapor</x-nav-item>
            @endif
            @if(auth()->user()->isAdmin())
            <div class="pt-md mt-md border-t border-outline-variant">
                <p class="text-caption text-on-surface-variant px-lg pb-xs uppercase tracking-wider">Admin</p>
                <x-nav-item href="{{ route('admin.dashboard') }}" icon="admin_panel_settings" :active="request()->routeIs('admin.dashboard')">Panel</x-nav-item>
                <x-nav-item href="{{ route('admin.classrooms.index') }}" icon="meeting_room" :active="request()->routeIs('admin.classrooms.*')">Kelas</x-nav-item>
                <x-nav-item href="{{ route('admin.subjects.index') }}" icon="book" :active="request()->routeIs('admin.subjects.*')">Mapel</x-nav-item>
                <x-nav-item href="{{ route('admin.students.index') }}" icon="people" :active="request()->routeIs('admin.students.*')">Siswa</x-nav-item>
                <x-nav-item href="{{ route('admin.teacher-subjects.index') }}" icon="assignment_ind" :active="request()->routeIs('admin.teacher-subjects.*')">Mapping</x-nav-item>
                <x-nav-item href="{{ route('admin.schedules.index') }}" icon="calendar_month" :active="request()->routeIs('admin.schedules.*')">Jadwal</x-nav-item>
                <x-nav-item href="{{ route('admin.score-components.index') }}" icon="tune" :active="request()->routeIs('admin.score-components.*')">Bobot Nilai</x-nav-item>
                <x-nav-item href="{{ route('admin.activity-logs.index') }}" icon="history" :active="request()->routeIs('admin.activity-logs.*')">Log Aktivitas</x-nav-item>
            </div>
            @endif
        </nav>
        <div class="p-md mt-auto border-t border-outline-variant">
            <form method="POST" action="{{ route('auth.logout.web') }}">
                @csrf
                <button type="submit" class="flex items-center gap-md px-sm text-on-surface-variant hover:text-error transition-colors w-full">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    <span class="text-label-md">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="lg:ml-64 px-margin-mobile md:px-margin-desktop py-lg pb-32 md:pb-0 max-w-7xl mx-auto">
        @yield('content')
    </main>

    {{-- BottomNavBar (mobile) --}}
    @if(auth()->user()->isWaliMurid())
    <nav class="md:hidden fixed bottom-0 w-full z-50 bg-surface-container-lowest border-t border-outline-variant shadow-md flex justify-around items-center h-16 px-margin-mobile">
        <a href="{{ route('wali-murid.dashboard') }}" class="flex flex-col items-center justify-center px-4 py-1 @if(request()->routeIs('wali-murid.dashboard')) text-primary @else text-on-surface-variant @endif">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="text-label-md">Dashboard</span>
        </a>
        <a href="{{ auth()->user()->students->first() ? route('wali-murid.rapor', auth()->user()->students->first()) : '#' }}" class="flex flex-col items-center justify-center px-4 py-1 @if(request()->routeIs('wali-murid.rapor')) text-primary @else text-on-surface-variant @endif">
            <span class="material-symbols-outlined">assignment</span>
            <span class="text-label-md">Rapor</span>
        </a>
    </nav>
    @else
    <nav class="md:hidden fixed bottom-0 w-full z-50 bg-surface-container-lowest border-t border-outline-variant shadow-md flex justify-around items-center h-16 px-margin-mobile">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center px-4 py-1 @if(request()->routeIs('dashboard')) text-primary @else text-on-surface-variant @endif">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="text-label-md">Dashboard</span>
        </a>
        <a href="{{ route('attendances.form') }}" class="flex flex-col items-center justify-center px-4 py-1 @if(request()->routeIs('attendances.*')) text-primary @else text-on-surface-variant @endif">
            <span class="material-symbols-outlined">how_to_reg</span>
            <span class="text-label-md">Absensi</span>
        </a>
        <a href="{{ route('schedules.mobile') }}" class="flex flex-col items-center justify-center px-4 py-1 @if(request()->routeIs('schedules.*')) text-primary @else text-on-surface-variant @endif">
            <span class="material-symbols-outlined">calendar_month</span>
            <span class="text-label-md">Jadwal</span>
        </a>
        <a href="{{ route('scores.create') }}" class="flex flex-col items-center justify-center px-4 py-1 @if(request()->routeIs('scores.*')) text-primary @else text-on-surface-variant @endif">
            <span class="material-symbols-outlined">grade</span>
            <span class="text-label-md">Nilai</span>
        </a>
        <a href="{{ route('scores.rapor-preview') }}" class="flex flex-col items-center justify-center px-4 py-1 @if(request()->routeIs('scores.rapor-preview')) text-primary @else text-on-surface-variant @endif">
            <span class="material-symbols-outlined">assignment</span>
            <span class="text-label-md">Rapor</span>
        </a>
    </nav>
    @endif
    @else
    <main class="min-h-screen flex items-center justify-center">
        @yield('content')
    </main>
    @endauth

    @stack('scripts')
</body>
</html>
