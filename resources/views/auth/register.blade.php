@extends('layouts.auth')

@section('title', 'Daftar Akun')

@section('content')
<div>
    <h2 class="text-2xl font-bold text-slate-800 mb-1">Buat Akun Baru ✨</h2>
    <p class="text-slate-500 mb-8">Mulai perjalanan hidup sehat Anda hari ini.</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="form-label">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   class="form-input @error('name') border-red-400 @enderror"
                   placeholder="Nama Anda" autocomplete="name" autofocus required>
            @error('name')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-input @error('email') border-red-400 @enderror"
                   placeholder="nama@email.com" autocomplete="email" required>
            @error('email')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password"
                   class="form-input @error('password') border-red-400 @enderror"
                   placeholder="Minimal 8 karakter" autocomplete="new-password" required>
            @error('password')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="form-input"
                   placeholder="Ulangi password" autocomplete="new-password" required>
        </div>

        <!-- Targets (Optional) -->
        <div class="bg-slate-50 rounded-xl p-4 space-y-3">
            <p class="text-sm font-semibold text-slate-600">🎯 Target Harian (Opsional)</p>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="calorie_target" class="form-label text-xs">Target Kalori (kkal)</label>
                    <input id="calorie_target" type="number" name="calorie_target"
                           value="{{ old('calorie_target', 2000) }}"
                           class="form-input" placeholder="2000" min="500" max="10000">
                </div>
                <div>
                    <label for="protein_target" class="form-label text-xs">Target Protein (g)</label>
                    <input id="protein_target" type="number" name="protein_target"
                           value="{{ old('protein_target', 150) }}"
                           class="form-input" placeholder="150" min="10" max="500">
                </div>
            </div>
        </div>

        <!-- Cloudflare Turnstile -->
        <div class="my-4 flex justify-center">
            <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
        </div>
        @error('cf-turnstile-response')
            <p class="mt-1 text-sm text-red-500 text-center">{{ $message }}</p>
        @enderror

        <!-- Submit -->
        <button type="submit" class="btn-primary w-full">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            Buat Akun
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:text-primary-700">
            Masuk di sini
        </a>
    </p>
</div>
@endsection
