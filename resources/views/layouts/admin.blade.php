<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Smart Calorie Tracker - Admin Panel">
    <title>@yield('title', 'Admin') — Smart Calorie Tracker</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

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

<body class="bg-slate-50 font-sans antialiased">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop"
        class="sidebar-backdrop hidden"
        aria-hidden="true"></div>

    <!-- Dashboard Shell -->
    <div class="dashboard-shell">

        <!-- Admin Sidebar -->
        <aside id="admin-sidebar" class="dashboard-sidebar admin-sidebar">
            <!-- Logo -->
            <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
                <div
                    class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-screwdriver-wrench text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-white font-bold text-sm leading-tight">Admin Panel</p>
                    <p class="text-slate-400 text-xs">Smart Calorie Tracker</p>
                </div>
                <!-- Mobile close button -->
                <button id="sidebar-close-btn" type="button"
                    class="ml-auto md:hidden p-1 rounded-lg text-slate-400 hover:text-slate-200 hover:bg-white/10 transition-colors"
                    aria-label="Tutup sidebar">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 mb-3">Manajemen</p>

                <a href="{{ route('admin.dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-house w-5 text-center flex-shrink-0"></i>
                    <span>Dashboard Admin</span>
                </a>

                <a href="{{ route('admin.foods.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.foods.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-pizza-slice w-5 text-center flex-shrink-0"></i>
                    <span>Master Makanan</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users w-5 text-center flex-shrink-0"></i>
                    <span>Manajemen User</span>
                </a>

                <div class="pt-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 mb-3">Navigasi</p>
                </div>

                <a href="{{ route('dashboard') }}" class="sidebar-link">
                    <i class="fa-solid fa-arrow-left w-5 text-center flex-shrink-0"></i>
                    <span>Kembali ke App</span>
                </a>
            </nav>

            <!-- User Info & Logout -->
            <div class="p-4 border-t border-white/10">
                <div class="flex items-center gap-3 mb-3">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                        class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                    <div class="min-w-0 flex-1">
                        <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                        <span class="badge bg-violet-500/20 text-violet-300 text-[10px]">Admin</span>
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

        <!-- Main Content Area -->
        <div class="dashboard-main">
            <!-- Top Header -->
            <header class="sticky top-0 z-30 bg-white border-b border-slate-100 px-4 lg:px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <!-- Mobile hamburger button -->
                        <button id="sidebar-open-btn" type="button"
                            class="md:hidden p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-500 transition-colors"
                            aria-label="Buka sidebar">
                            <i class="fa-solid fa-bars text-base"></i>
                        </button>
                        <div>
                            <h1 class="text-lg font-bold text-slate-800">@yield('page-title', 'Admin Dashboard')</h1>
                            <p class="text-sm text-slate-500">@yield('page-subtitle', '')</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Dark/Light Switcher Button -->
                        <button id="theme-toggle" type="button"
                            class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-500 dark:bg-slate-800 dark:hover:bg-slate-700 dark:border-slate-700 dark:text-slate-400 transition-colors duration-150 cursor-pointer"
                            aria-label="Toggle theme">
                            <!-- Sun Icon (shown in dark mode) -->
                            <i id="theme-toggle-light-icon"
                                class="hidden fa-solid fa-sun w-5 h-5 text-center leading-5"></i>
                            <!-- Moon Icon (shown in light mode) -->
                            <i id="theme-toggle-dark-icon"
                                class="hidden fa-solid fa-moon w-5 h-5 text-center leading-5"></i>
                        </button>

                        <span class="badge bg-violet-100 text-violet-700 text-xs font-semibold"><i
                                class="fa-solid fa-lock mr-1"></i> Admin Mode</span>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <div class="px-4 lg:px-6 pt-4">
                @if(session('success'))
                    <div class="alert-success mb-4">
                        <i class="fa-solid fa-circle-check flex-shrink-0"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert-error mb-4">
                        <i class="fa-solid fa-circle-xmark flex-shrink-0"></i>
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="flex-1 px-4 lg:px-6 py-4 pb-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar JS -->
    <script>
        (function () {
            const openBtn = document.getElementById('sidebar-open-btn');
            const closeBtn = document.getElementById('sidebar-close-btn');
            const backdrop = document.getElementById('sidebar-backdrop');
            const sidebar = document.getElementById('admin-sidebar');

            function openSidebar() {
                sidebar.classList.add('sidebar-open');
                backdrop.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.remove('sidebar-open');
                backdrop.classList.add('hidden');
                document.body.style.overflow = '';
            }

            if (openBtn) openBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);

            // Close sidebar on nav link click (mobile)
            if (sidebar) {
                sidebar.querySelectorAll('a.sidebar-link').forEach(function (link) {
                    link.addEventListener('click', function () {
                        if (window.innerWidth < 768) closeSidebar();
                    });
                });
            }
        })();
    </script>

    @stack('scripts')
</body>

</html>