<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Smart Calorie Tracker - Lacak kalori, protein, dan perkembangan berat badan Anda dengan bantuan AI">

    <title>@yield('title', 'Dashboard') — Smart Calorie Tracker</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Dark Mode Inline Theme Protection -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 font-sans antialiased">
<div class="flex h-full">

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 w-64 flex flex-col z-50 app-sidebar">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
            <div class="w-9 h-9 rounded-xl stat-gradient-green flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-chart-column text-white text-lg"></i>
            </div>
            <div>
                <p class="text-white font-bold text-sm leading-tight">Smart Calorie</p>
                <p class="text-slate-400 text-xs">Tracker</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 mb-3">Menu Utama</p>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-house w-5 text-center flex-shrink-0"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('scanner.index') }}"
               class="sidebar-link {{ request()->routeIs('scanner.*') ? 'active' : '' }}">
                <i class="fa-solid fa-camera w-5 text-center flex-shrink-0"></i>
                <span>Scan Makanan AI</span>
                <span class="ml-auto badge-green text-[10px] px-1.5 py-0.5 rounded-full">AI</span>
            </a>

            <a href="{{ route('food-log.index') }}"
               class="sidebar-link {{ request()->routeIs('food-log.*') ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-list w-5 text-center flex-shrink-0"></i>
                <span>Jurnal Makanan</span>
            </a>

            <a href="{{ route('weight-log.index') }}"
               class="sidebar-link {{ request()->routeIs('weight-log.*') ? 'active' : '' }}">
                <i class="fa-solid fa-weight-scale w-5 text-center flex-shrink-0"></i>
                <span>Fat Loss Tracker</span>
            </a>

            <div class="pt-4">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 mb-3">Akun</p>
            </div>

            <a href="{{ route('profile.edit') }}"
               class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user w-5 text-center flex-shrink-0"></i>
                <span>Profil Saya</span>
            </a>
        </nav>

        <!-- User Info & Logout -->
        <div class="p-4 border-t border-white/10">
            <div class="flex items-center gap-3 mb-3">
                <img src="{{ auth()->user()->avatar_url }}"
                     alt="{{ auth()->user()->name }}"
                     class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                <div class="min-w-0 flex-1">
                    <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-slate-400 text-xs truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg">
                    <i class="fa-solid fa-right-from-bracket text-sm"></i>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col ml-64 min-h-full">
        <!-- Top Header -->
        <header class="sticky top-0 z-40 bg-white border-b border-slate-100 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-sm text-slate-500">@yield('page-subtitle', '')</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-500 mr-1">{{ now()->translatedFormat('l, d F Y') }}</span>
                    
                    <!-- Dark/Light Switcher Button -->
                    <button id="theme-toggle" type="button" class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-500 dark:bg-slate-800 dark:hover:bg-slate-700 dark:border-slate-700 dark:text-slate-400 transition-colors duration-150 cursor-pointer" aria-label="Toggle theme">
                        <!-- Sun Icon (shown in dark mode) -->
                        <i id="theme-toggle-light-icon" class="hidden fa-solid fa-sun w-5 h-5 text-center leading-5"></i>
                        <!-- Moon Icon (shown in light mode) -->
                        <i id="theme-toggle-dark-icon" class="hidden fa-solid fa-moon w-5 h-5 text-center leading-5"></i>
                    </button>

                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.dashboard') }}"
                           class="btn-secondary btn-sm text-xs">
                            <i class="fa-solid fa-gear mr-1"></i> Admin Panel
                        </a>
                    @endif
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="alert-success mb-4" role="alert">
                    <i class="fa-solid fa-circle-check flex-shrink-0"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert-error mb-4" role="alert">
                    <i class="fa-solid fa-circle-xmark flex-shrink-0"></i>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="flex-1 px-6 py-4 pb-8">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
