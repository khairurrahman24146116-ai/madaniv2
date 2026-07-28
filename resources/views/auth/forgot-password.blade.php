<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Madani Al-Aziziyah - Lupa Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="bg-background text-on-surface font-sans antialiased">
<div class="min-h-screen flex flex-col md:flex-row">
    <div class="hidden md:flex md:w-1/2 bg-[#004ac6] min-h-screen flex-col items-center justify-center p-12 relative">
        <div class="absolute inset-0 opacity-[0.08]">
            <div class="absolute top-10 left-10 w-80 h-80 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>
        <div class="relative z-10 text-center max-w-[360px]">
            <div class="w-20 h-20 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-8 border border-white/20">
                <span class="material-symbols-outlined text-[44px] text-white">school</span>
            </div>
            <h1 class="text-[36px] font-bold text-white leading-tight tracking-tight mb-4">Madani Al-Aziziyah</h1>
            <p class="text-[16px] text-white/70 leading-relaxed">Sistem Informasi Manajemen SMA Sore Dayah Madani Al-Aziziyah</p>
        </div>
    </div>
    <div class="w-full md:w-1/2 min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-[400px]">
            <div class="md:hidden text-center mb-8">
                <div class="w-14 h-14 bg-[#004ac6] text-white rounded-xl flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[28px]">school</span>
                </div>
                <h1 class="text-[22px] font-bold text-on-surface">Madani Al-Aziziyah</h1>
            </div>
            <div class="mb-8">
                <h2 class="text-[24px] font-semibold text-on-surface tracking-tight">Lupa Password</h2>
                <p class="text-[14px] text-on-surface-variant mt-1">Masukkan email Anda untuk menerima tautan reset password</p>
            </div>
            @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 text-green-800 rounded-xl text-[14px] flex items-start gap-3 border border-green-200">
                <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">check_circle</span>
                <div>{{ session('status') }}</div>
            </div>
            @endif
            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 text-red-800 rounded-xl text-[14px] flex items-start gap-3 border border-red-200">
                <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">error</span>
                <div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            <div class="bg-white border border-[#c3c6d7] rounded-xl p-8 shadow-sm">
                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="block text-[12px] font-semibold text-[#434655] uppercase tracking-wider mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full rounded-lg border border-[#c3c6d7] bg-white text-[#191c1e] px-4 py-3 text-[14px] outline-none focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 transition-colors"
                            autocomplete="email" placeholder="admin@madani.id">
                    </div>
                    <button type="submit"
                        class="w-full bg-[#2563eb] text-white font-semibold text-[16px] py-3 rounded-lg flex items-center justify-center gap-2 hover:bg-[#2563eb]/90 active:scale-[0.98] transition-all duration-150 shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">send</span>
                        Kirim Tautan Reset
                    </button>
                </form>
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-[14px] text-[#2563eb] hover:underline inline-flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                        Kembali ke Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
