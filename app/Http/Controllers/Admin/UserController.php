<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index(Request $request)
    {
        $query = User::role('user')->withCount(['foodLogs', 'weightLogs']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users      = $query->latest()->paginate(15)->withQueryString();
        $totalUsers = User::role('user')->count();

        return view('admin.users.index', compact('users', 'totalUsers'));
    }

    /**
     * Show user detail.
     */
    public function show(User $user)
    {
        $user->load(['foodLogs' => function($q) {
            $q->latest()->take(10);
        }, 'weightLogs' => function($q) {
            $q->latest('date')->take(5);
        }]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat menghapus akun admin.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
