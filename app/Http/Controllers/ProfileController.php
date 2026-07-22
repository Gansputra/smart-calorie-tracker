<?php

namespace App\Http\Controllers;

use App\Services\AiServerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct(
        protected AiServerService $aiServerService
    ) {}

    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }


    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'calorie_target'  => ['nullable', 'integer', 'min:500', 'max:10000'],
            'protein_target'  => ['nullable', 'integer', 'min:10', 'max:500'],
            'gender'          => ['nullable', 'in:male,female'],
            'age'             => ['nullable', 'integer', 'min:10', 'max:120'],
            'height'          => ['nullable', 'numeric', 'min:50', 'max:300'],
            'weight'          => ['nullable', 'numeric', 'min:20', 'max:500'],
            'activity_level' => ['nullable', 'integer', 'in:0,1,2,3'],
            'goal'           => ['nullable', 'integer', 'in:0,1,2'],
            'avatar'          => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ], [
            'name.required'   => 'Nama wajib diisi.',
            'email.required'  => 'Email wajib diisi.',
            'email.unique'    => 'Email ini sudah digunakan.',
            'avatar.image'    => 'Avatar harus berupa gambar.',
            'avatar.max'      => 'Ukuran avatar maksimal 2MB.',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return redirect()->route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui! ✅');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required'      => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required'              => 'Password baru wajib diisi.',
            'password.min'                   => 'Password minimal 8 karakter.',
            'password.confirmed'             => 'Konfirmasi password tidak cocok.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Password berhasil diperbarui! 🔒');
    }

    /**
     * Get AI recommendation for daily calorie and protein targets.
     */
    public function recommendTargets(Request $request)
    {
        $validated = $request->validate([
            'gender'         => ['required', 'integer', 'in:0,1'],
            'age'            => ['required', 'integer', 'min:10', 'max:120'],
            'weight'         => ['required', 'numeric', 'min:30', 'max:250'],
            'height'         => ['required', 'numeric', 'min:100', 'max:250'],
            'activity_level' => ['required', 'integer', 'in:0,1,2,3'],
            'goal'           => ['required', 'integer', 'in:0,1,2'],
        ]);

        $result = $this->aiServerService->recommendTargets($validated);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Gagal menghubungi AI Server.',
            ], 422);
        }

        return response()->json([
            'success'              => true,
            'recommended_calories' => $result['recommended_calories'],
            'recommended_protein'  => $result['recommended_protein'],
            'source'               => $result['source'] ?? 'machine_learning',
        ]);
    }
}

