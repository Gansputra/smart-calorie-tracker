<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $rules = [
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'calorie_target'        => ['nullable', 'integer', 'min:500', 'max:10000'],
            'protein_target'        => ['nullable', 'integer', 'min:10', 'max:500'],
        ];

        if (config('services.turnstile.secret_key')) {
            $rules['cf-turnstile-response'] = ['required', new \App\Rules\Turnstile()];
        }

        $validated = $request->validate($rules, [
            'name.required'                  => 'Nama wajib diisi.',
            'email.required'                 => 'Email wajib diisi.',
            'email.email'                    => 'Format email tidak valid.',
            'email.unique'                   => 'Email ini sudah terdaftar.',
            'password.required'              => 'Password wajib diisi.',
            'password.min'                   => 'Password minimal 8 karakter.',
            'password.confirmed'             => 'Konfirmasi password tidak cocok.',
            'cf-turnstile-response.required' => 'Verifikasi "Saya bukan robot" wajib diselesaikan.',
        ]);

        $user = User::create([
            'name'            => $validated['name'],
            'email'           => $validated['email'],
            'password'        => Hash::make($validated['password']),
            'calorie_target'  => $validated['calorie_target'] ?? 2000,
            'protein_target'  => $validated['protein_target'] ?? 150,
        ]);

        $user->assignRole('user');

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Selamat datang di Smart Calorie Tracker! 🎉');
    }
}
