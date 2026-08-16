<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="view-transition" content="same-origin">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <title>Madani Al-Aziziyah @isset($title) - {{ $title }} @endisset</title>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400..700&family=Inter:wght@100..900&family=JetBrains+Mono:wght@400;500;600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&family=Source+Serif+4:opsz,wght@8..60,400;8..60,600;8..60,700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="bg-background text-on-surface font-sans antialiased min-h-screen">
    @auth
    {{-- Top App Bar --}}
    <x-top-app-bar />
    
    {{-- Navigation Data --}}
    @php
        $navItems = [];
        
        if (auth()->user()->isWaliMurid()) {
            $firstStudentId = auth()->user()->students()->first()?->id;
            $navItems = [
                ['label' => 'Dashboard', 'icon' => 'dashboard', 'href' => route('wali-murid.dashboard'), 'activeRoutes' => ['wali-murid.dashboard']],
                ['label' => 'Rapor', 'icon' => 'assignment', 'href' => $firstStudentId ? route('wali-murid.rapor', $firstStudentId) : route('wali-murid.dashboard'), 'activeRoutes' => ['wali-murid.rapor']],
                ['label' => 'Surat Aktif', 'icon' => 'badge', 'href' => route('active-letters.index'), 'activeRoutes' => ['active-letters.*']],
                ['label' => 'SPP', 'icon' => 'payments', 'href' => route('spp.index'), 'activeRoutes' => ['spp.*']],
                ['label' => 'Surat', 'icon' => 'description', 'href' => route('wali.letters.index'), 'activeRoutes' => ['wali.letters.*']],
                ['label' => 'Hubungi Kepsek', 'icon' => 'mail', 'href' => route('wali.contact.index'), 'activeRoutes' => ['wali.contact.*']],
                ['label' => 'Jadwal Pertemuan', 'icon' => 'event', 'href' => route('wali.meetings.index'), 'activeRoutes' => ['wali.meetings.*']],
            ];
        } elseif (auth()->user()->isBendahara()) {
            $navItems = [
                ['label' => 'Dashboard', 'icon' => 'dashboard', 'href' => route('bendahara.dashboard'), 'activeRoutes' => ['bendahara.dashboard']],
                ['label' => 'SPP', 'icon' => 'payments', 'href' => route('spp.index'), 'activeRoutes' => ['spp.*']],
                ['label' => 'Rekap', 'icon' => 'summarize', 'href' => route('bendahara.rekap'), 'activeRoutes' => ['bendahara.rekap']],
            ];
        } elseif (auth()->user()->isGuru()) {
            $navItems = [
                ['label' => 'Dashboard', 'icon' => 'dashboard', 'href' => route('dashboard'), 'activeRoutes' => ['dashboard']],
                ['label' => 'Absensi Siswa', 'icon' => 'how_to_reg', 'href' => route('attendances.form'), 'activeRoutes' => ['attendances.*']],
                ['label' => 'Absensi Guru', 'icon' => 'badge', 'href' => route('teacher.attendances.form'), 'activeRoutes' => ['teacher.attendances.*']],
                ['label' => 'Riwayat Absen Guru', 'icon' => 'history', 'href' => route('teacher.attendances.index'), 'activeRoutes' => ['teacher.attendances.index']],
                ['label' => 'Jadwal', 'icon' => 'calendar_month', 'href' => route('schedules.index'), 'activeRoutes' => ['schedules.*']],
                ['label' => 'Nilai', 'icon' => 'grade', 'href' => route('scores.create'), 'activeRoutes' => ['scores.*']],
                ['label' => 'E-Rapor', 'icon' => 'assignment', 'href' => route('scores.rapor-preview'), 'activeRoutes' => ['scores.rapor-preview']],
                ['label' => 'SPP', 'icon' => 'payments', 'href' => route('spp.index'), 'activeRoutes' => ['spp.*']],
                ['label' => 'Surat', 'icon' => 'description', 'href' => route('guru.letters.index'), 'activeRoutes' => ['guru.letters.*']],
            ];
        }
        
        $adminItems = [
            ['divider' => true, 'label' => 'Admin'],
            ['label' => 'Panel', 'icon' => 'admin_panel_settings', 'href' => route('admin.dashboard'), 'activeRoutes' => ['admin.dashboard']],
            ['label' => 'Kelas', 'icon' => 'meeting_room', 'href' => route('admin.classrooms.index'), 'activeRoutes' => ['admin.classrooms.*']],
            ['label' => 'Mapel', 'icon' => 'book', 'href' => route('admin.subjects.index'), 'activeRoutes' => ['admin.subjects.*']],
            ['label' => 'Siswa', 'icon' => 'people', 'href' => route('admin.students.index'), 'activeRoutes' => ['admin.students.*']],
            ['label' => 'Mapping', 'icon' => 'assignment_ind', 'href' => route('admin.teacher-subjects.index'), 'activeRoutes' => ['admin.teacher-subjects.*']],
            ['label' => 'Jadwal', 'icon' => 'calendar_month', 'href' => route('admin.schedules.index'), 'activeRoutes' => ['admin.schedules.*']],
            ['label' => 'Absensi Guru', 'icon' => 'badge', 'href' => route('admin.teacher-attendances.index'), 'activeRoutes' => ['admin.teacher-attendances.*']],
            ['label' => 'Pengguna', 'icon' => 'manage_accounts', 'href' => route('admin.users.index'), 'activeRoutes' => ['admin.users.*']],
            ['label' => 'Bobot Nilai', 'icon' => 'tune', 'href' => route('admin.score-components.index'), 'activeRoutes' => ['admin.score-components.*']],
            ['label' => 'SPP', 'icon' => 'payments', 'href' => route('spp.index'), 'activeRoutes' => ['spp.*']],
            ['label' => 'Surat', 'icon' => 'description', 'href' => route('admin.letters.index'), 'activeRoutes' => ['admin.letters.*']],
            ['label' => 'Pesan Masuk', 'icon' => 'mail', 'href' => route('admin.contact.index'), 'activeRoutes' => ['admin.contact.*']],
            ['label' => 'Pertemuan', 'icon' => 'event', 'href' => route('admin.meetings.index'), 'activeRoutes' => ['admin.meetings.*']],
            ['label' => 'Log Aktivitas', 'icon' => 'history', 'href' => route('admin.activity-logs.index'), 'activeRoutes' => ['admin.activity-logs.*']],
        ];
        
        if (auth()->user()->isAdmin()) {
            $navItems = array_merge($navItems, $adminItems);
        }
        
        $bottomNavItems = match (true) {
            auth()->user()->isWaliMurid() => [
                ['label' => 'Home', 'icon' => 'home', 'href' => route('wali-murid.dashboard'), 'activeRoutes' => ['wali-murid.dashboard']],
                ['label' => 'SPP', 'icon' => 'payments', 'href' => route('spp.index'), 'activeRoutes' => ['spp.*']],
                ['label' => 'Surat', 'icon' => 'grade', 'href' => route('wali.letters.index'), 'activeRoutes' => ['wali.letters.*', 'active-letters.*']],
                ['label' => 'Profil', 'icon' => 'person', 'href' => route('profile.edit'), 'activeRoutes' => ['profile.*']],
            ],
            auth()->user()->isBendahara() => [
                ['label' => 'Home', 'icon' => 'home', 'href' => route('bendahara.dashboard'), 'activeRoutes' => ['bendahara.dashboard']],
                ['label' => 'SPP', 'icon' => 'payments', 'href' => route('spp.index'), 'activeRoutes' => ['spp.*']],
                ['label' => 'Rekap', 'icon' => 'summarize', 'href' => route('bendahara.rekap'), 'activeRoutes' => ['bendahara.rekap']],
                ['label' => 'Profil', 'icon' => 'person', 'href' => route('profile.edit'), 'activeRoutes' => ['profile.*']],
            ],
            auth()->user()->isAdmin() => [
                ['label' => 'Home', 'icon' => 'home', 'href' => route('admin.dashboard'), 'activeRoutes' => ['admin.dashboard']],
                ['label' => 'Jadwal', 'icon' => 'calendar_month', 'href' => route('admin.schedules.index'), 'activeRoutes' => ['admin.schedules.*']],
                ['label' => 'Siswa', 'icon' => 'grade', 'href' => route('admin.students.index'), 'activeRoutes' => ['admin.students.*']],
                ['label' => 'Profil', 'icon' => 'person', 'href' => route('profile.edit'), 'activeRoutes' => ['profile.*']],
            ],
            default => [
                ['label' => 'Home', 'icon' => 'home', 'href' => route('dashboard'), 'activeRoutes' => ['dashboard']],
                ['label' => 'Jadwal', 'icon' => 'calendar_month', 'href' => route('schedules.index'), 'activeRoutes' => ['schedules.*']],
                ['label' => 'Nilai', 'icon' => 'grade', 'href' => route('scores.create'), 'activeRoutes' => ['scores.*']],
                ['label' => 'Profil', 'icon' => 'person', 'href' => route('profile.edit'), 'activeRoutes' => ['profile.*']],
            ],
        };
    @endphp
    
    {{-- Nav Rail (Desktop) --}}
    <x-nav-rail :items="$navItems" />
    
    {{-- Bottom Nav (Mobile) --}}
    <x-bottom-nav :items="$bottomNavItems" />
    
    {{-- Main Content --}}
    <main class="md:ml-nav-rail-width px-margin-mobile md:px-margin-desktop py-6 pb-24 max-w-[var(--container-max)] mx-auto animate-fade-in">
        @yield('content')
    </main>
    
    {{-- Hamburger Drawer (Mobile) --}}
    <x-hamburger-drawer :items="$navItems" />
    
    @else
    <main class="min-h-screen flex items-center justify-center">
        @yield('content')
    </main>
    @endauth

    <x-toast />
    <x-confirm-modal />

    @stack('scripts')

    <script>
        (function() {
            var toggle = document.getElementById('hamburger-toggle');
            var drawer = document.getElementById('hamburger-drawer');
            var overlay = document.getElementById('hamburger-overlay');
            var close = document.getElementById('hamburger-close');
            
            // Hamburger drawer
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
            
            // User menu dropdown
            var userMenuToggle = document.getElementById('user-menu-toggle');
            var userMenu = document.getElementById('user-menu');
            if (userMenuToggle && userMenu) {
                userMenuToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                    var expanded = !userMenu.classList.contains('hidden');
                    userMenuToggle.setAttribute('aria-expanded', expanded);
                });
                document.addEventListener('click', function(e) {
                    if (!userMenuToggle.contains(e.target) && !userMenu.contains(e.target)) {
                        userMenu.classList.add('hidden');
                        userMenuToggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        })();
    </script>
</body>
</html>