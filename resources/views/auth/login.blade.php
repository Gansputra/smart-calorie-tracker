@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
<div>
    <h2 class="text-2xl font-bold text-slate-800 mb-1">Selamat Datang 👋</h2>
    <p class="text-slate-500 mb-8">Masuk untuk melanjutkan ke dashboard Anda.</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}"
                   class="form-input @error('email') border-red-400 ring-2 ring-red-400/30 @enderror"
                   placeholder="nama@email.com"
                   autocomplete="email" autofocus required>
            @error('email')
                <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="form-label">Password</label>
            <div class="relative">
                <input id="password" type="password" name="password"
                       class="form-input @error('password') border-red-400 ring-2 ring-red-400/30 @enderror pr-12"
                       placeholder="••••••••"
                       autocomplete="current-password" required>
                <button type="button" id="togglePassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <svg id="eyeIcon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                <span class="text-sm text-slate-600">Ingat Saya</span>
            </label>
        </div>

        <!-- Cloudflare Turnstile -->
        <div class="my-4 flex justify-center">
            <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
        </div>
        @error('cf-turnstile-response')
            <p class="mt-1 text-sm text-red-500 text-center">{{ $message }}</p>
        @enderror

        <!-- Submit -->
        <button type="submit" class="btn-primary w-full mt-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
            Masuk
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-primary-600 font-semibold hover:text-primary-700">
            Daftar sekarang
        </a>
    </p>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
});
</script>
@endsection
