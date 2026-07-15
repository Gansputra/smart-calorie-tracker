<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Smart Calorie Tracker — Masuk atau daftar untuk mulai melacak kalori Anda">
    <title>@yield('title', 'Masuk') — Smart Calorie Tracker</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 font-sans antialiased">
<div class="min-h-full flex">
    <!-- Left Panel (Decorative) -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden" style="background: linear-gradient(135deg, #0f1923 0%, #1a3a2a 50%, #0f1923 100%);">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-64 h-64 rounded-full bg-green-400 blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-80 h-80 rounded-full bg-green-600 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col justify-center items-center text-center px-12 py-16 w-full">
            <!-- Logo -->
            <div class="w-20 h-20 rounded-3xl stat-gradient-green flex items-center justify-center mb-6 shadow-2xl shadow-green-500/30">
                <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>

            <h1 class="text-4xl font-extrabold text-white mb-4">Smart Calorie<br>Tracker</h1>
            <p class="text-slate-300 text-lg max-w-sm leading-relaxed mb-10">
                Lacak kalori, protein, dan perkembangan berat badan Anda dengan bantuan teknologi AI.
            </p>

            <!-- Feature Highlights -->
            <div class="space-y-4 w-full max-w-sm text-left">
                @foreach([
                    ['icon' => '📸', 'title' => 'Scan Makanan AI', 'desc' => 'Foto makanan → prediksi kalori otomatis'],
                    ['icon' => '📊', 'title' => 'Dashboard Cerdas', 'desc' => 'Visualisasi kemajuan harian Anda'],
                    ['icon' => '⚖️', 'title' => 'Fat Loss Tracker', 'desc' => 'Grafik perkembangan berat badan'],
                ] as $feature)
                    <div class="flex items-center gap-4 bg-white/10 rounded-xl px-4 py-3 backdrop-blur-sm">
                        <span class="text-2xl">{{ $feature['icon'] }}</span>
                        <div>
                            <p class="text-white font-semibold text-sm">{{ $feature['title'] }}</p>
                            <p class="text-slate-400 text-xs">{{ $feature['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Panel (Form) -->
    <div class="flex-1 flex flex-col justify-center py-12 px-6 sm:px-12 lg:px-16 xl:px-24 bg-white">
        <div class="mx-auto w-full max-w-sm">
            <!-- Mobile logo -->
            <div class="flex items-center gap-3 mb-8 lg:hidden">
                <div class="w-10 h-10 rounded-2xl stat-gradient-green flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <span class="font-bold text-slate-800">Smart Calorie Tracker</span>
            </div>

            @yield('content')
        </div>
    </div>
</div>
</body>
</html>
