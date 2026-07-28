<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Madani Al-Aziziyah @isset($title) - {{ $title }} @endisset</title>
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
                        <h1 class="text-headline-lg-mobile md:text-headline-lg font-bold text-on-surface leading-none">Madani Al-Aziziyah</h1>
                        <p class="hidden sm:block text-caption text-on-surface-variant mt-1">SMA Sore Dayah Madani</p>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-sm md:gap-md">
                <span class="text-label-md text-on-surface-variant hidden md:block">{{ auth()->user()->name }}</span>
                <button id="dark-toggle" class="p-sm hover:bg-surface-container-high rounded-md transition-colors" title="Ubah tema" aria-label="Ubah tema">
                    <span class="material-symbols-outlined text-on-surface-variant" id="theme-icon">dark_mode</span>
                </button>
                <div class="relative">
                    <button id="user-menu-toggle" type="button" class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container font-bold border border-outline-variant overflow-hidden hover:ring-2 hover:ring-primary/40 transition-shadow" aria-label="Menu pengguna" aria-haspopup="true" aria-expanded="false">
                        @if(auth()->user()->profile_photo_url)
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="" class="w-full h-full object-cover">
                        @else
                            <span>{{ substr(auth()->user()->name, 0, 1) }}</span>
                        @endif
                    </button>
                    <div id="user-menu" class="hidden absolute right-0 mt-2 w-56 bg-surface-container-lowest border border-outline-variant rounded-lg shadow-lg overflow-hidden">
                        <div class="px-md py-sm border-b border-outline-variant">
                            <p class="text-label-md font-semibold text-on-surface truncate">{{ auth()->user()->name }}</p>
                            <p class="text-caption text-on-surface-variant truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-md w-full px-md py-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                            <span class="text-label-md">Profil</span>
                        </a>
                        <form method="POST" action="{{ route('auth.logout.web') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-md w-full px-md py-sm text-on-surface-variant hover:bg-surface-container-high hover:text-error transition-colors">
                                <span class="material-symbols-outlined text-[20px]">logout</span>
                                <span class="text-label-md">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
                <button id="hamburger-toggle" type="button" class="lg:hidden p-sm hover:bg-surface-container-high rounded-md transition-colors" aria-label="Buka menu" aria-expanded="false">
                    <span class="material-symbols-outlined text-on-surface-variant text-2xl">menu</span>
                </button>
            </div>
        </div>
    </header>

    {{-- SideNavBar (desktop) --}}
    <aside class="hidden lg:flex h-[calc(100vh-64px)] w-64 flex-col bg-surface-container-low border-r border-outline-variant fixed left-0 top-16 z-40 overflow-y-auto">
        <nav class="flex-1 space-y-xs py-md">
            @if(auth()->user()->isWaliMurid())
            <x-nav-item href="{{ route('wali-murid.dashboard') }}" icon="dashboard" :active="request()->routeIs('wali-murid.dashboard')">Dashboard</x-nav-item>
            <x-nav-item href="{{ route('wali.letters.index') }}" icon="description" :active="request()->routeIs('wali.letters.*')">Surat</x-nav-item>
            <x-nav-item href="{{ route('wali.contact.index') }}" icon="mail" :active="request()->routeIs('wali.contact.*')">Hubungi Kepsek</x-nav-item>
            <x-nav-item href="{{ route('wali.meetings.index') }}" icon="event" :active="request()->routeIs('wali.meetings.*')">Jadwal Pertemuan</x-nav-item>
            @elseif(auth()->user()->isGuru())
            <x-nav-item href="{{ route('dashboard') }}" icon="dashboard" :active="request()->routeIs('dashboard')">Dashboard</x-nav-item>
            <x-nav-item href="{{ route('attendances.form') }}" icon="how_to_reg" :active="request()->routeIs('attendances.*')">Absensi Siswa</x-nav-item>
            <x-nav-item href="{{ route('teacher.attendances.form') }}" icon="badge" :active="request()->routeIs('teacher.attendances.*')">Absensi Guru</x-nav-item>
            <x-nav-item href="{{ route('teacher.attendances.index') }}" icon="history" :active="request()->routeIs('teacher.attendances.index')">Riwayat Absen Guru</x-nav-item>
            <x-nav-item href="{{ route('schedules.index') }}" icon="calendar_month" :active="request()->routeIs('schedules.*')">Jadwal</x-nav-item>
            <x-nav-item href="{{ route('scores.create') }}" icon="grade" :active="request()->routeIs('scores.*')">Nilai</x-nav-item>
            <x-nav-item href="{{ route('scores.rapor-preview') }}" icon="assignment" :active="request()->routeIs('scores.rapor-preview')">E-Rapor</x-nav-item>
            <x-nav-item href="{{ route('guru.letters.index') }}" icon="description" :active="request()->routeIs('guru.letters.*')">Surat</x-nav-item>
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
                <x-nav-item href="{{ route('admin.teacher-attendances.index') }}" icon="badge" :active="request()->routeIs('admin.teacher-attendances.*')">Absensi Guru</x-nav-item>
                <x-nav-item href="{{ route('admin.users.index') }}" icon="manage_accounts" :active="request()->routeIs('admin.users.*')">Pengguna</x-nav-item>
                <x-nav-item href="{{ route('admin.score-components.index') }}" icon="tune" :active="request()->routeIs('admin.score-components.*')">Bobot Nilai</x-nav-item>
                <x-nav-item href="{{ route('admin.letters.index') }}" icon="description" :active="request()->routeIs('admin.letters.*')">Surat</x-nav-item>
                <x-nav-item href="{{ route('admin.contact.index') }}" icon="mail" :active="request()->routeIs('admin.contact.*')">Pesan Masuk</x-nav-item>
                <x-nav-item href="{{ route('admin.meetings.index') }}" icon="event" :active="request()->routeIs('admin.meetings.*')">Pertemuan</x-nav-item>
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
    <main class="lg:ml-64 px-margin-mobile md:px-margin-desktop py-lg pb-lg max-w-7xl mx-auto">
        @yield('content')
    </main>

    {{-- Hamburger Drawer (mobile) --}}
    <div id="hamburger-drawer" class="lg:hidden fixed inset-0 z-50 hidden">
        <div id="hamburger-overlay" class="absolute inset-0 bg-black/40"></div>
        <div class="absolute left-0 top-0 h-full w-72 bg-surface-container-lowest shadow-xl border-r border-outline-variant overflow-y-auto">
            <div class="flex items-center justify-between px-lg py-md border-b border-outline-variant">
                <span class="text-headline-md font-bold text-on-surface">Menu</span>
                <button id="hamburger-close" type="button" class="p-1 text-on-surface-variant hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <nav class="py-md space-y-xs">
                @if(auth()->user()->isWaliMurid())
                    <x-nav-item href="{{ route('wali-murid.dashboard') }}" icon="dashboard" :active="request()->routeIs('wali-murid.dashboard')">Dashboard</x-nav-item>
                    <x-nav-item href="{{ route('wali.letters.index') }}" icon="description" :active="request()->routeIs('wali.letters.*')">Surat</x-nav-item>
                    <x-nav-item href="{{ route('wali.contact.index') }}" icon="mail" :active="request()->routeIs('wali.contact.*')">Hubungi Kepsek</x-nav-item>
                    <x-nav-item href="{{ route('wali.meetings.index') }}" icon="event" :active="request()->routeIs('wali.meetings.*')">Jadwal Pertemuan</x-nav-item>
                    <x-nav-item href="{{ auth()->user()->students->first() ? route('wali-murid.rapor', auth()->user()->students->first()) : '#' }}" icon="assignment" :active="request()->routeIs('wali-murid.rapor')">Rapor</x-nav-item>
                @elseif(auth()->user()->isGuru())
                    <x-nav-item href="{{ route('dashboard') }}" icon="dashboard" :active="request()->routeIs('dashboard')">Dashboard</x-nav-item>
                    <x-nav-item href="{{ route('attendances.form') }}" icon="how_to_reg" :active="request()->routeIs('attendances.*')">Absensi Siswa</x-nav-item>
                    <x-nav-item href="{{ route('teacher.attendances.form') }}" icon="badge" :active="request()->routeIs('teacher.attendances.*')">Absensi Guru</x-nav-item>
                    <x-nav-item href="{{ route('teacher.attendances.index') }}" icon="history" :active="request()->routeIs('teacher.attendances.index')">Riwayat Absen Guru</x-nav-item>
                    <x-nav-item href="{{ route('schedules.index') }}" icon="calendar_month" :active="request()->routeIs('schedules.*')">Jadwal</x-nav-item>
                    <x-nav-item href="{{ route('scores.create') }}" icon="grade" :active="request()->routeIs('scores.*')">Nilai</x-nav-item>
                    <x-nav-item href="{{ route('scores.rapor-preview') }}" icon="assignment" :active="request()->routeIs('scores.rapor-preview')">E-Rapor</x-nav-item>
                    <x-nav-item href="{{ route('guru.letters.index') }}" icon="description" :active="request()->routeIs('guru.letters.*')">Surat</x-nav-item>
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
                        <x-nav-item href="{{ route('admin.teacher-attendances.index') }}" icon="badge" :active="request()->routeIs('admin.teacher-attendances.*')">Absensi Guru</x-nav-item>
                        <x-nav-item href="{{ route('admin.users.index') }}" icon="manage_accounts" :active="request()->routeIs('admin.users.*')">Pengguna</x-nav-item>
                        <x-nav-item href="{{ route('admin.score-components.index') }}" icon="tune" :active="request()->routeIs('admin.score-components.*')">Bobot Nilai</x-nav-item>
                        <x-nav-item href="{{ route('admin.letters.index') }}" icon="description" :active="request()->routeIs('admin.letters.*')">Surat</x-nav-item>
                        <x-nav-item href="{{ route('admin.contact.index') }}" icon="mail" :active="request()->routeIs('admin.contact.*')">Pesan Masuk</x-nav-item>
                        <x-nav-item href="{{ route('admin.meetings.index') }}" icon="event" :active="request()->routeIs('admin.meetings.*')">Pertemuan</x-nav-item>
                        <x-nav-item href="{{ route('admin.activity-logs.index') }}" icon="history" :active="request()->routeIs('admin.activity-logs.*')">Log Aktivitas</x-nav-item>
                    </div>
                @endif
            </nav>
            <div class="p-md border-t border-outline-variant">
                <form method="POST" action="{{ route('auth.logout.web') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-md w-full px-sm py-2 text-on-surface-variant hover:text-error transition-colors">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                        <span class="text-label-md">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @else
    <main class="min-h-screen flex items-center justify-center">
        @yield('content')
    </main>
    @endauth

    @stack('scripts')

    <script>
        (function() {
            var toggle = document.getElementById('hamburger-toggle');
            var drawer = document.getElementById('hamburger-drawer');
            var overlay = document.getElementById('hamburger-overlay');
            var close = document.getElementById('hamburger-close');
            if (toggle && drawer) {
                function openDrawer() {
                    drawer.classList.remove('hidden');
                    toggle.setAttribute('aria-expanded', 'true');
                    document.body.style.overflow = 'hidden';
                }
                function closeDrawer() {
                    drawer.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                    document.body.style.overflow = '';
                }
                toggle.addEventListener('click', openDrawer);
                if (overlay) overlay.addEventListener('click', closeDrawer);
                if (close) close.addEventListener('click', closeDrawer);
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') closeDrawer();
                });
            }
        })();
    </script>
</body>
</html>
