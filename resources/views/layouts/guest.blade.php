<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Madani Al-Aziziyah @isset($title) - {{ $title }} @endisset</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=JetBrains+Mono:wght@400;500&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
</head>
<body class="bg-surface text-on-surface min-h-screen flex items-center justify-center font-body-lg text-body-lg">
    <div class="fixed inset-0 bg-primary/5 pointer-events-none"></div>
    <main class="w-full max-w-md p-margin-mobile md:p-0 relative z-10">
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
