<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $rules = [
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ];

        if (config('services.turnstile.secret_key')) {
            $rules['cf-turnstile-response'] = ['required', new \App\Rules\Turnstile()];
        }

        $credentials = $request->validate($rules, [
            'email.required'                 => 'Email wajib diisi.',
            'email.email'                    => 'Format email tidak valid.',
            'password.required'              => 'Password wajib diisi.',
            'cf-turnstile-response.required' => 'Verifikasi "Saya bukan robot" wajib diselesaikan.',
        ]);

        $remember = $request->boolean('remember');
        $loginData = ['email' => $credentials['email'], 'password' => $credentials['password']];

        if (Auth::attempt($loginData, $remember)) {
            $request->session()->regenerate();

            return $this->redirectBasedOnRole();
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ]);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil keluar.');
    }

    /**
     * Redirect based on user role.
     */
    protected function redirectBasedOnRole()
    {
        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
