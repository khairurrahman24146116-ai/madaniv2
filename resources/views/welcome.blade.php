<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Madani-SMS - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="bg-background text-on-surface font-sans antialiased">

<div class="min-h-screen flex flex-col md:flex-row">
    {{-- Left Panel: Branding --}}
    <div class="hidden md:flex md:w-1/2 bg-[#004ac6] min-h-screen flex-col items-center justify-center p-12 relative">
        <div class="absolute inset-0 opacity-[0.08]">
            <div class="absolute top-10 left-10 w-80 h-80 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>
        <div class="relative z-10 text-center max-w-[360px]">
            <div class="w-20 h-20 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-8 border border-white/20">
                <span class="material-symbols-outlined text-[44px] text-white">school</span>
            </div>
            <h1 class="text-[36px] font-bold text-white leading-tight tracking-tight mb-4">Madani-SMS</h1>
            <p class="text-[16px] text-white/70 leading-relaxed">Sistem Informasi Manajemen SMA Sore Dayah Madani Al-Aziziyah</p>
            <div class="flex items-center justify-center gap-6 mt-10 text-white/50 text-[14px]">
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                    <span>Jadwal</span>
                </div>
                <div class="w-1 h-1 bg-white/30 rounded-full"></div>
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                    <span>Absensi</span>
                </div>
                <div class="w-1 h-1 bg-white/30 rounded-full"></div>
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">grade</span>
                    <span>Nilai</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Panel: Form --}}
    <div class="w-full md:w-1/2 min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-[400px]">

            {{-- Mobile Logo --}}
            <div class="md:hidden text-center mb-8">
                <div class="w-14 h-14 bg-[#004ac6] text-white rounded-xl flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[28px]">school</span>
                </div>
                <h1 class="text-[22px] font-bold text-on-surface">Madani-SMS</h1>
                <p class="text-[14px] text-on-surface-variant mt-1">SMA Sore Dayah Madani Al-Aziziyah</p>
            </div>

            {{-- Welcome Text --}}
            <div class="mb-8">
                <h2 class="text-[24px] font-semibold text-on-surface tracking-tight">Selamat Datang</h2>
                <p class="text-[14px] text-on-surface-variant mt-1">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            {{-- Error Message --}}
            @if(session('errors'))
                <div class="mb-6 p-4 bg-red-50 text-red-800 rounded-xl text-[14px] flex items-start gap-3 border border-red-200">
                    <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">error</span>
                    <div>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach(session('errors')->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Login Card --}}
            <div class="bg-white border border-[#c3c6d7] rounded-xl p-8 shadow-sm">
                <form method="POST" action="{{ route('auth.login.web') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-[12px] font-semibold text-[#434655] uppercase tracking-wider mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="w-full rounded-lg border border-[#c3c6d7] bg-white text-[#191c1e] px-4 py-3 text-[14px] outline-none focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 transition-colors"
                               autocomplete="email" placeholder="admin@madani.id">
                    </div>

                    <div>
                        <label for="password" class="block text-[12px] font-semibold text-[#434655] uppercase tracking-wider mb-2">Password</label>
                        <input type="password" name="password" id="password" required
                               class="w-full rounded-lg border border-[#c3c6d7] bg-white text-[#191c1e] px-4 py-3 text-[14px] outline-none focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 transition-colors"
                               autocomplete="current-password" placeholder="Masukkan password">
                    </div>

                    <button type="submit"
                            class="w-full bg-[#2563eb] text-white font-semibold text-[16px] py-3 rounded-lg flex items-center justify-center gap-2 hover:bg-[#2563eb]/90 active:scale-[0.98] transition-all duration-150 shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">login</span>
                        Masuk
                    </button>
                </form>
            </div>

            {{-- Demo Credentials --}}
            <div class="mt-6 p-4 bg-[#f2f4f6] border border-[#c3c6d7] rounded-xl">
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-[16px] text-[#434655]">info</span>
                    <p class="text-[12px] font-semibold text-[#434655] uppercase tracking-wider">Demo Credentials</p>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[14px] text-[#434655]">Admin</span>
                        <code class="font-mono text-[12px] bg-white px-2 py-1 rounded border border-[#c3c6d7]">admin@madani.id / admin123</code>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[14px] text-[#434655]">Guru</span>
                        <code class="font-mono text-[12px] bg-white px-2 py-1 rounded border border-[#c3c6d7]">ahmad@madani.id / guru123</code>
                    </div>
                </div>
            </div>

            <p class="text-center text-[12px] text-[#434655] mt-8">
                Sistem Informasi Manajemen Sekolah v1.0.0
            </p>
        </div>
    </div>
</div>

</body>
</html>
