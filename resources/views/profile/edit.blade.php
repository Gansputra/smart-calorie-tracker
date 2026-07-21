@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title')
    <i class="fa-solid fa-user-gear mr-2 text-primary-600 dark:text-primary-400"></i> Profil &amp; Target Nutrisi
@endsection
@section('page-subtitle', 'Kelola informasi pribadi, data fisik, dan preferensi target nutrisi Anda')

@section('content')
    <style>
        /* ── Entrance animation ── */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-up {
            animation: slideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .delay-100 {
            animation-delay: 0.10s;
        }

        .delay-200 {
            animation-delay: 0.20s;
        }

        .delay-300 {
            animation-delay: 0.30s;
        }

        .delay-400 {
            animation-delay: 0.40s;
        }

        /* ── Profile card: dark bg always, refined glow ── */
        .profile-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0f172a 100%);
            position: relative;
            overflow: hidden;
        }

        .profile-hero::before {
            content: '';
            position: absolute;
            width: 380px;
            height: 380px;
            right: -80px;
            top: -80px;
            background: radial-gradient(circle, rgba(34, 197, 94, 0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .profile-hero::after {
            content: '';
            position: absolute;
            width: 320px;
            height: 320px;
            left: -60px;
            bottom: -60px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ── Section card ── */
        .section-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            padding: 1.75rem 2rem;
            transition: box-shadow 0.25s, border-color 0.25s;
        }

        .section-card:hover {
            box-shadow: 0 8px 32px -4px rgba(0, 0, 0, 0.07), 0 2px 8px -2px rgba(0, 0, 0, 0.04);
            border-color: #cbd5e1;
        }

        .dark .section-card {
            background-color: #111827;
            border-color: #1f2937;
        }

        .dark .section-card:hover {
            box-shadow: 0 8px 32px -4px rgba(0, 0, 0, 0.4);
            border-color: #374151;
        }

        /* ── Section header divider ── */
        .section-divider {
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .dark .section-divider {
            border-bottom-color: #1f2937;
        }

        /* ── Hero Active Status Badge ── */
        @keyframes activeGlow {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(52, 211, 153, 0), 0 2px 8px rgba(16, 185, 129, 0.25);
            }

            50% {
                box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.15), 0 2px 12px rgba(16, 185, 129, 0.4);
            }
        }

        @keyframes activeDot {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(0.7);
            }
        }

        .active-badge {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.7rem 0.3rem 0.5rem;
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            color: #6ee7b7;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(52, 211, 153, 0.3);
            border-radius: 999px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            animation: activeGlow 2.5s ease-in-out infinite;
            user-select: none;
            white-space: nowrap;
        }

        .active-badge-dot {
            width: 5px;
            height: 5px;
            background: #34d399;
            border-radius: 50%;
            flex-shrink: 0;
            animation: activeDot 1.6s ease-in-out infinite;
        }

        /* ── AI Live Engine Badge ── */
        @keyframes badgeShimmer {
            0% {
                left: -80%;
            }

            100% {
                left: 130%;
            }
        }

        @keyframes badgePulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.6);
            }

            60% {
                box-shadow: 0 0 0 5px rgba(34, 197, 94, 0);
            }
        }

        .ai-live-badge {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.75rem 0.3rem 0.5rem;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #ffffff;
            background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
            border-radius: 999px;
            box-shadow: 0 2px 10px rgba(16, 185, 129, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.15);
            user-select: none;
            white-space: nowrap;
        }

        .ai-live-badge-dot {
            width: 6px;
            height: 6px;
            background: #ffffff;
            border-radius: 50%;
            flex-shrink: 0;
            animation: badgePulse 1.8s ease-in-out infinite;
        }

        .ai-live-badge-shine {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 40%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: badgeShimmer 2.4s ease-in-out infinite;
            pointer-events: none;
        }


        .field-input {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.875rem;
            background-color: #f8fafc;
            color: #0f172a;
            font-size: 0.8125rem;
            font-weight: 500;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
        }

        .field-input:focus {
            border-color: #22c55e;
            background-color: #ffffff;
            box-shadow: 0 0 0 3.5px rgba(34, 197, 94, 0.15);
        }

        .field-input::placeholder {
            color: #94a3b8;
        }

        .dark .field-input {
            background-color: #1f2937;
            border-color: #374151;
            color: #f9fafb;
        }

        .dark .field-input:focus {
            border-color: #22c55e;
            background-color: #1f2937;
            box-shadow: 0 0 0 3.5px rgba(34, 197, 94, 0.2);
        }

        select.field-input {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.625rem center;
            background-repeat: no-repeat;
            background-size: 1.25em 1.25em;
            padding-right: 2.25rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            cursor: pointer;
        }

        /* ── Field label ── */
        .field-label {
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.375rem;
        }

        .dark .field-label {
            color: #94a3b8;
        }

        /* ── Mode toggle card ── */
        .mode-card {
            display: flex;
            align-items: center;
            padding: 1rem 1.25rem;
            border: 2px solid #e2e8f0;
            border-radius: 1rem;
            background-color: #ffffff;
            cursor: pointer;
            transition: border-color 0.2s, background-color 0.2s, box-shadow 0.2s, transform 0.2s;
            user-select: none;
        }

        .mode-card:hover {
            border-color: #86efac;
            box-shadow: 0 4px 16px -4px rgba(34, 197, 94, 0.15);
            transform: translateY(-1px);
        }

        .dark .mode-card {
            background-color: #1a2535;
            border-color: #1f2937;
        }

        .dark .mode-card:hover {
            border-color: #166534;
            box-shadow: 0 4px 16px -4px rgba(34, 197, 94, 0.12);
        }

        .mode-card.is-active {
            border-color: #22c55e;
            background-color: #f0fdf4;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.12), 0 4px 16px -4px rgba(34, 197, 94, 0.2);
        }

        .dark .mode-card.is-active {
            border-color: #22c55e;
            background-color: rgba(34, 197, 94, 0.08);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15), 0 4px 16px -4px rgba(34, 197, 94, 0.15);
        }

        /* ── Mode icon box ── */
        .mode-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
            transition: background-color 0.2s, color 0.2s;
        }

        /* ── AI Result box ── */
        .ai-result-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            padding: 1.25rem 1.5rem;
            transition: all 0.3s;
        }

        .dark .ai-result-box {
            background-color: #0f172a;
            border-color: #1f2937;
        }

        /* ── AI stat chip ── */
        .ai-stat-chip {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.875rem;
            padding: 1rem;
            text-align: center;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .ai-stat-chip:hover {
            border-color: #86efac;
            box-shadow: 0 4px 16px -4px rgba(34, 197, 94, 0.12);
        }

        .dark .ai-stat-chip {
            background-color: #111827;
            border-color: #1f2937;
        }

        .dark .ai-stat-chip:hover {
            border-color: #166534;
        }

        /* ── Check bubble ── */
        .check-bubble {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            background-color: #22c55e;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            flex-shrink: 0;
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.2s;
        }

        .check-bubble.hidden-check {
            transform: scale(0);
            opacity: 0;
            pointer-events: none;
            display: flex !important;
        }

        /* ── Stat chip in hero ── */
        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.875rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 0.875rem;
            backdrop-filter: blur(8px);
            font-size: 0.75rem;
            color: #e2e8f0;
            transition: background 0.2s, border-color 0.2s;
        }

        .hero-chip:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* ── Hero stat card (desktop right column) ── */
        .hero-stat-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1rem 1.25rem;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            min-width: 5.5rem;
            transition: background 0.2s, border-color 0.2s, transform 0.2s;
        }

        .hero-stat-card:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.18);
            transform: translateY(-2px);
        }

        /* ── Hero responsive: stat grid (desktop) & chips (mobile) ── */
        .hero-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(5.5rem, 1fr));
            gap: 0.75rem;
            margin-left: auto;
            flex-shrink: 0;
        }

        .hero-chips-mobile {
            display: none;
        }

        @media (max-width: 639px) {
            .hero-stats-grid {
                display: none;
            }

            .hero-chips-mobile {
                display: flex;
            }
        }

        /* ── Submit buttons ── */
        .btn-save {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 2rem;
            font-size: 0.8125rem;
            font-weight: 700;
            color: #ffffff;
            background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            border-radius: 0.875rem;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(22, 163, 74, 0.3);
            transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
            letter-spacing: 0.01em;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(22, 163, 74, 0.35);
            filter: brightness(1.05);
        }

        .btn-save:active {
            transform: translateY(0) scale(0.97);
        }

        .btn-password {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.75rem;
            font-size: 0.8125rem;
            font-weight: 700;
            color: #ffffff;
            background-color: #1e293b;
            border-radius: 0.875rem;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: transform 0.2s, box-shadow 0.2s, background-color 0.2s;
            letter-spacing: 0.01em;
        }

        .btn-password:hover {
            background-color: #0f172a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-password:active {
            transform: translateY(0) scale(0.97);
        }

        .dark .btn-password {
            background-color: #374151;
        }

        .dark .btn-password:hover {
            background-color: #4b5563;
        }

        /* ── Section icon badge ── */
        .section-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        /* ── Manual input section transition ── */
        #aiRecommendationSection,
        #manualInputsSection {
            transition: opacity 0.3s, transform 0.3s;
        }
    </style>

    <div class="max-w-4xl mx-auto space-y-6 font-sans">

        {{-- ══ Hero / Profile Summary Banner ══ --}}
        <div class="profile-hero rounded-2xl p-6 sm:p-8 text-white shadow-xl animate-slide-up">
            <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-center gap-6">

                {{-- Avatar --}}
                <div class="relative group shrink-0" id="avatarContainer">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                        class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover border-2 border-white/15 shadow-2xl transition-transform duration-300 group-hover:scale-[1.04]"
                        id="avatarPreview">
                    <label
                        class="absolute -bottom-2 -right-2 w-8 h-8 bg-primary-500 hover:bg-primary-400 text-white rounded-xl flex items-center justify-center cursor-pointer shadow-lg transition-all duration-200 hover:scale-110 active:scale-95">
                        <i class="fa-solid fa-camera text-xs"></i>
                        <input type="file" form="profileForm" name="avatar" accept="image/*" class="hidden"
                            id="avatarInput">
                    </label>
                    <div id="avatarDragOverlay"
                        class="absolute inset-0 rounded-2xl bg-primary-600/90 text-white flex flex-col items-center justify-center transition-all z-10 pointer-events-none"
                        style="display:none;">
                        <i class="fa-solid fa-cloud-arrow-up text-xl animate-bounce"></i>
                        <span class="text-[10px] font-bold mt-1">Drop foto</span>
                    </div>
                </div>

                {{-- Identity Info --}}
                <div class="text-center sm:text-left">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <h2 class="text-xl font-black tracking-tight text-white leading-snug">{{ $user->name }}</h2>
                        <span class="active-badge">
                            <span class="active-badge-dot"></span>
                            Active
                        </span>
                    </div>
                    <p class="mt-2 text-[13px] text-slate-300 flex items-center justify-center sm:justify-start gap-2">
                        <i class="fa-regular fa-envelope text-slate-400 text-xs"></i>
                        {{ $user->email }}
                    </p>

                    {{-- Mobile only: inline chips --}}
                    <div class="hero-chips-mobile mt-4 flex-wrap items-center justify-center gap-2">
                        <div class="hero-chip">
                            <i class="fa-solid fa-weight-scale text-primary-400 text-xs"></i>
                            <span class="font-semibold text-sm">{{ $user->weight ?? 65 }} kg</span>
                        </div>
                        <div class="hero-chip">
                            <i class="fa-solid fa-ruler-vertical text-emerald-400 text-xs"></i>
                            <span class="font-semibold text-sm">{{ $user->height ?? 170 }} cm</span>
                        </div>
                        <div class="hero-chip">
                            <i class="fa-solid fa-fire text-amber-400 text-xs"></i>
                            <span class="font-semibold text-sm">{{ $user->calorie_target ?? 2000 }} kkal/hari</span>
                        </div>
                    </div>
                </div>

                {{-- Desktop only: Stat Cards Grid (fills right space) --}}
                <div class="hero-stats-grid">
                    <div class="hero-stat-card">
                        <i class="fa-solid fa-weight-scale text-primary-400 mb-2"></i>
                        <p class="text-2xl font-black text-white leading-none">{{ $user->weight ?? 65 }}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1.5">Berat</p>
                        <p class="text-[10px] text-slate-500 tracking-wide">kg</p>
                    </div>
                    <div class="hero-stat-card">
                        <i class="fa-solid fa-ruler-vertical text-emerald-400 mb-2"></i>
                        <p class="text-2xl font-black text-white leading-none">{{ $user->height ?? 170 }}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1.5">Tinggi</p>
                        <p class="text-[10px] text-slate-500 tracking-wide">cm</p>
                    </div>
                    <div class="hero-stat-card">
                        <i class="fa-solid fa-fire text-amber-400 mb-2"></i>
                        <p class="text-2xl font-black text-white leading-none">{{ $user->calorie_target ?? 2000 }}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1.5">Target</p>
                        <p class="text-[10px] text-slate-500 tracking-wide">kkal/hari</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- ══ Main Profile Form ══ --}}
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6"
            id="profileForm">
            @csrf
            @method('PUT')

            {{-- ─ Section 1: Informasi Akun ─ --}}
            <div class="section-card animate-slide-up delay-100">
                <div class="section-divider flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="section-icon bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400">
                            <i class="fa-regular fa-id-card"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">Informasi Akun</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Identitas dasar pengguna</p>
                        </div>
                    </div>
                    <span
                        class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg tracking-wide uppercase">User
                        Info</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label for="name" class="field-label">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                            class="field-input @error('name') !border-red-400 !ring-red-100 @enderror" required>
                        @error('name')
                            <p class="text-[11px] text-red-500 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="email" class="field-label">Alamat Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                            class="field-input @error('email') !border-red-400 !ring-red-100 @enderror" required>
                        @error('email')
                            <p class="text-[11px] text-red-500 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ─ Section 2: Data Fisiologis & Aktivitas ─ --}}
            <div class="section-card animate-slide-up delay-200">
                <div class="section-divider flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="section-icon bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">Data Fisiologis
                                &amp; Aktivitas</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Parameter tubuh yang digunakan
                                dalam kalkulasi AI</p>
                        </div>
                    </div>
                    <span class="ai-live-badge">
                        <span class="ai-live-badge-dot"></span>
                        <i class="fa-solid fa-microchip-ai" style="font-size:0.6rem;"></i>
                        <span>AI Engine</span>
                        <span class="ai-live-badge-shine"></span>
                    </span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="space-y-1.5">
                        <label for="gender" class="field-label">Jenis Kelamin</label>
                        <select name="gender" id="gender" class="field-input">
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Laki-Laki
                            </option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Perempuan
                            </option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label for="age" class="field-label">Usia (Tahun)</label>
                        <input type="number" name="age" id="age" value="{{ old('age', $user->age ?? 25) }}"
                            class="field-input" min="10" max="120" placeholder="25">
                    </div>

                    <div class="space-y-1.5">
                        <label for="height" class="field-label">Tinggi (cm)</label>
                        <input type="number" name="height" id="height" value="{{ old('height', $user->height ?? 170) }}"
                            class="field-input" step="0.5" min="50" max="300" placeholder="170">
                    </div>

                    <div class="space-y-1.5">
                        <label for="weight_profile" class="field-label">Berat (kg)</label>
                        <input type="number" name="weight" id="weight_profile"
                            value="{{ old('weight', $user->weight ?? 65) }}" class="field-input" step="0.1" min="20"
                            max="500" placeholder="65">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="space-y-1.5">
                        <label for="activity_level" class="field-label">Tingkat Aktivitas Harian</label>
                        <select id="activity_level" class="field-input">
                            <option value="0">Minimal (Sedentary - Jarang Olahraga)</option>
                            <option value="1" selected>Ringan (Light - Olahraga 1-3x/minggu)</option>
                            <option value="2">Sedang (Moderate - Olahraga 3-5x/minggu)</option>
                            <option value="3">Tinggi (Active - Olahraga 6-7x/minggu)</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label for="goal" class="field-label">Tujuan Kesehatan (Goal)</label>
                        <select id="goal" class="field-input">
                            <option value="0" selected>Defisit Kalori (Fat Loss / Turun Berat)</option>
                            <option value="1">Menjaga Berat Badan (Maintenance)</option>
                            <option value="2">Surplus Kalori (Muscle Gain / Naik Berat)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- ─ Section 3: Target Nutrisi Harian ─ --}}
            <div class="section-card animate-slide-up delay-300">
                <div class="section-divider flex items-center gap-3">
                    <div class="section-icon bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">Target Nutrisi Harian
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Metode penentuan target kalori dan
                            protein harian</p>
                    </div>
                </div>

                {{-- Mode Selection --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                    {{-- AI Mode Card --}}
                    <label id="card_mode_ai" class="mode-card gap-4">
                        <input type="radio" name="target_mode" value="ai" id="mode_ai" class="sr-only" checked>
                        <div class="mode-icon bg-primary-50 text-primary-600 dark:bg-primary-950/40 dark:text-primary-400 text-base"
                            id="icon_mode_ai">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                        </div>
                        <div class="grow min-w-0">
                            <p class="text-sm font-bold text-slate-900 dark:text-white leading-snug">Rekomendasi AI</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">Otomatis oleh Machine
                                Learning</p>
                        </div>
                        <div id="check_ai" class="check-bubble shrink-0">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </label>

                    {{-- Manual Mode Card --}}
                    <label id="card_mode_manual" class="mode-card gap-4">
                        <input type="radio" name="target_mode" value="manual" id="mode_manual" class="sr-only">
                        <div class="mode-icon bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 text-base"
                            id="icon_mode_manual">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>
                        <div class="grow min-w-0">
                            <p class="text-sm font-bold text-slate-900 dark:text-white leading-snug">Kustom / Manual</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">Input angka target
                                secara manual</p>
                        </div>
                        <div id="check_manual" class="check-bubble hidden-check shrink-0">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </label>
                </div>

                {{-- Hidden inputs --}}
                <input type="hidden" name="calorie_target" id="hidden_calorie_target"
                    value="{{ old('calorie_target', $user->calorie_target) }}">
                <input type="hidden" name="protein_target" id="hidden_protein_target"
                    value="{{ old('protein_target', $user->protein_target) }}">

                {{-- 1. AI Recommendation Output (Exclusive) --}}
                <div id="aiRecommendationSection" class="space-y-3">
                    <div class="ai-result-box">
                        {{-- Header --}}
                        <div
                            class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-microchip text-primary-500 dark:text-primary-400"></i>
                                Hasil Rekomendasi AI Real-time
                            </span>
                            <span id="aiLiveStatus"
                                class="text-[10px] font-bold text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-950/50 border border-primary-100 dark:border-primary-900/50 px-2.5 py-1 rounded-lg">
                                Machine Learning Model
                            </span>
                        </div>

                        {{-- Stat chips --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="ai-stat-chip">
                                <p
                                    class="text-[10px] font-bold text-slate-500 dark:text-slate-400 tracking-widest uppercase mb-2">
                                    Target Kalori AI</p>
                                <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tight leading-none"
                                    id="liveCalories">
                                    <i class="fa-solid fa-spinner animate-spin text-slate-400 text-sm"></i>
                                </p>
                            </div>
                            <div class="ai-stat-chip">
                                <p
                                    class="text-[10px] font-bold text-slate-500 dark:text-slate-400 tracking-widest uppercase mb-2">
                                    Target Protein AI</p>
                                <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tight leading-none"
                                    id="liveProtein">
                                    <i class="fa-solid fa-spinner animate-spin text-slate-400 text-sm"></i>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Manual Custom Inputs (Exclusive) --}}
                <div id="manualInputsSection" class="grid grid-cols-2 gap-4" style="display:none;">
                    <div class="space-y-1.5">
                        <label for="manual_calorie_target" class="field-label">Target Kalori (kkal)</label>
                        <input type="number" id="manual_calorie_target"
                            value="{{ old('calorie_target', $user->calorie_target) }}"
                            class="field-input @error('calorie_target') !border-red-400 @enderror" min="500" max="10000"
                            placeholder="Contoh: 2000">
                    </div>
                    <div class="space-y-1.5">
                        <label for="manual_protein_target" class="field-label">Target Protein (g)</label>
                        <input type="number" id="manual_protein_target"
                            value="{{ old('protein_target', $user->protein_target) }}"
                            class="field-input @error('protein_target') !border-red-400 @enderror" min="10" max="500"
                            placeholder="Contoh: 120">
                    </div>
                </div>
            </div>

            {{-- Save Button --}}
            <div class="flex items-center justify-end pt-1 animate-slide-up delay-400">
                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-check text-xs"></i>
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>

        {{-- ══ Security & Password Card ══ --}}
        <div class="section-card animate-slide-up delay-400">
            <div class="section-divider flex items-center gap-3">
                <div class="section-icon bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">Keamanan Akun</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui kata sandi untuk menjaga keamanan
                        akun</p>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label for="current_password" class="field-label">Password Saat Ini</label>
                    <input type="password" name="current_password" id="current_password"
                        class="field-input @error('current_password') !border-red-400 @enderror">
                    @error('current_password')
                        <p class="text-[11px] text-red-500 font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="new_password" class="field-label">Password Baru</label>
                        <input type="password" name="password" id="new_password"
                            class="field-input @error('password') !border-red-400 @enderror"
                            placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="text-[11px] text-red-500 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="field-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="field-input"
                            placeholder="Ulangi password baru">
                    </div>
                </div>

                <div class="flex items-center justify-end pt-1">
                    <button type="submit" class="btn-password">
                        <i class="fa-solid fa-lock text-xs"></i>
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // --- Avatar Handling ---
        const avatarContainer = document.getElementById('avatarContainer');
        const avatarInput = document.getElementById('avatarInput');
        const avatarPreview = document.getElementById('avatarPreview');

        function updateAvatarPreview(file) {
            if (!file || !file.type.startsWith('image/')) return;
            try {
                const dt = new DataTransfer();
                dt.items.add(file);
                avatarInput.files = dt.files;
            } catch (e) { }
            const reader = new FileReader();
            reader.onload = e => { avatarPreview.src = e.target.result; };
            reader.readAsDataURL(file);
        }

        if (avatarInput) {
            avatarInput.addEventListener('change', function () {
                if (this.files[0]) updateAvatarPreview(this.files[0]);
            });
        }

        // --- Real-time AI Target Engine & Exclusive Display Logic ---
        const inputGender = document.getElementById('gender');
        const inputAge = document.getElementById('age');
        const inputHeight = document.getElementById('height');
        const inputWeight = document.getElementById('weight_profile');
        const inputActivity = document.getElementById('activity_level');
        const inputGoal = document.getElementById('goal');

        const modeAi = document.getElementById('mode_ai');
        const modeManual = document.getElementById('mode_manual');

        const cardModeAi = document.getElementById('card_mode_ai');
        const cardModeManual = document.getElementById('card_mode_manual');

        const checkAi = document.getElementById('check_ai');
        const checkManual = document.getElementById('check_manual');

        const iconModeAi = document.getElementById('icon_mode_ai');
        const iconModeManual = document.getElementById('icon_mode_manual');

        const aiRecommendationSection = document.getElementById('aiRecommendationSection');
        const manualInputsSection = document.getElementById('manualInputsSection');

        const liveCalories = document.getElementById('liveCalories');
        const liveProtein = document.getElementById('liveProtein');
        const liveAiStatus = document.getElementById('aiLiveStatus');

        const hiddenCalorieTarget = document.getElementById('hidden_calorie_target');
        const hiddenProteinTarget = document.getElementById('hidden_protein_target');
        const manualCalorieTarget = document.getElementById('manual_calorie_target');
        const manualProteinTarget = document.getElementById('manual_protein_target');

        let latestAiCalories = 0;
        let latestAiProtein = 0;
        let debounceTimer = null;

        async function fetchRealtimeAiRecommendation() {
            liveCalories.innerHTML = `<i class="fa-solid fa-spinner animate-spin text-slate-400 text-sm"></i>`;
            liveProtein.innerHTML = `<i class="fa-solid fa-spinner animate-spin text-slate-400 text-sm"></i>`;

            const payload = {
                gender: inputGender.value === 'male' ? 1 : 0,
                age: parseInt(inputAge.value) || 25,
                weight: parseFloat(inputWeight.value) || 65,
                height: parseFloat(inputHeight.value) || 170,
                activity_level: parseInt(inputActivity.value) || 1,
                goal: parseInt(inputGoal.value) || 0,
            };

            try {
                const response = await fetch("{{ route('profile.recommend-targets') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    latestAiCalories = data.recommended_calories;
                    latestAiProtein = data.recommended_protein;

                    liveCalories.textContent = `${data.recommended_calories} kkal`;
                    liveProtein.textContent = `${data.recommended_protein} g`;
                    liveAiStatus.textContent = data.source === 'machine_learning' ? 'Machine Learning Model' : 'Formula Medis';

                    if (modeAi.checked) {
                        syncAiValuesToHiddenInputs();
                    }
                } else {
                    liveCalories.textContent = '—';
                    liveProtein.textContent = '—';
                }
            } catch (err) {
                liveCalories.textContent = 'Offline';
                liveProtein.textContent = 'Offline';
                liveAiStatus.textContent = 'Server Disconnected';
            }
        }

        function syncAiValuesToHiddenInputs() {
            if (latestAiCalories > 0) hiddenCalorieTarget.value = latestAiCalories;
            if (latestAiProtein > 0) hiddenProteinTarget.value = latestAiProtein;
        }

        function syncManualValuesToHiddenInputs() {
            hiddenCalorieTarget.value = manualCalorieTarget.value;
            hiddenProteinTarget.value = manualProteinTarget.value;
        }

        function handleModeToggle() {
            if (modeAi.checked) {
                // ── AI active ──
                aiRecommendationSection.style.display = 'block';
                manualInputsSection.style.display = 'none';

                // Check bubbles
                checkAi.classList.remove('hidden-check');
                checkManual.classList.add('hidden-check');

                // Card styles
                cardModeAi.classList.add('is-active');
                cardModeManual.classList.remove('is-active');

                // Icon accent
                iconModeAi.style.backgroundColor = '';
                iconModeAi.style.color = '';
                iconModeAi.classList.remove('bg-slate-100', 'text-slate-600', 'dark:bg-slate-800', 'dark:text-slate-300');
                iconModeAi.classList.add('bg-primary-100', 'text-primary-700', 'dark:bg-primary-950/60', 'dark:text-primary-300');

                iconModeManual.classList.remove('bg-primary-100', 'text-primary-700', 'dark:bg-primary-950/60', 'dark:text-primary-300');
                iconModeManual.classList.add('bg-slate-100', 'text-slate-600', 'dark:bg-slate-800', 'dark:text-slate-300');

                syncAiValuesToHiddenInputs();
            } else {
                // ── Manual active ──
                aiRecommendationSection.style.display = 'none';
                manualInputsSection.style.display = 'grid';

                // Check bubbles
                checkManual.classList.remove('hidden-check');
                checkAi.classList.add('hidden-check');

                // Card styles
                cardModeManual.classList.add('is-active');
                cardModeAi.classList.remove('is-active');

                // Icon accent
                iconModeManual.classList.remove('bg-slate-100', 'text-slate-600', 'dark:bg-slate-800', 'dark:text-slate-300');
                iconModeManual.classList.add('bg-primary-100', 'text-primary-700', 'dark:bg-primary-950/60', 'dark:text-primary-300');

                iconModeAi.classList.remove('bg-primary-100', 'text-primary-700', 'dark:bg-primary-950/60', 'dark:text-primary-300');
                iconModeAi.classList.add('bg-slate-100', 'text-slate-600', 'dark:bg-slate-800', 'dark:text-slate-300');

                syncManualValuesToHiddenInputs();
            }
        }

        function triggerDebounceCalculation() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchRealtimeAiRecommendation();
            }, 300);
        }

        // Event Listeners
        [inputGender, inputAge, inputHeight, inputWeight, inputActivity, inputGoal].forEach(element => {
            if (element) {
                element.addEventListener('input', triggerDebounceCalculation);
                element.addEventListener('change', triggerDebounceCalculation);
            }
        });

        if (manualCalorieTarget) manualCalorieTarget.addEventListener('input', syncManualValuesToHiddenInputs);
        if (manualProteinTarget) manualProteinTarget.addEventListener('input', syncManualValuesToHiddenInputs);

        if (modeAi) modeAi.addEventListener('change', handleModeToggle);
        if (modeManual) modeManual.addEventListener('change', handleModeToggle);

        document.addEventListener('DOMContentLoaded', () => {
            fetchRealtimeAiRecommendation();
            handleModeToggle();
        });
    </script>
@endsection