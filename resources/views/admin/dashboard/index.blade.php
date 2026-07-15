@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title')
    <i class="fa-solid fa-gear mr-1.5"></i> Dashboard Admin
@endsection
@section('page-subtitle', 'Ringkasan dan statistik aplikasi Smart Calorie Tracker')

@section('content')
<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl stat-gradient-blue flex items-center justify-center">
                    <i class="fa-solid fa-users text-white text-lg"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-800">{{ $totalUsers }}</p>
            <p class="text-slate-500 text-sm mt-1">Total User</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl stat-gradient-green flex items-center justify-center">
                    <i class="fa-solid fa-clipboard-list text-white text-lg"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-800">{{ number_format($totalFoodLogs) }}</p>
            <p class="text-slate-500 text-sm mt-1">Total Food Logs</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl stat-gradient-orange flex items-center justify-center">
                    <i class="fa-solid fa-pizza-slice text-white text-lg"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-800">{{ $totalFoods }}</p>
            <p class="text-slate-500 text-sm mt-1">Master Makanan</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl stat-gradient-purple flex items-center justify-center">
                    <i class="fa-solid fa-user-shield text-white text-lg"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-800">{{ $totalAdmins }}</p>
            <p class="text-slate-500 text-sm mt-1">Total Admin</p>
        </div>
    </div>

    {{-- Recent Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Users --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800"><i class="fa-solid fa-users mr-1"></i> User Terbaru</h3>
                <a href="{{ route('admin.users.index') }}" class="text-xs text-primary-600 hover:underline">Lihat Semua →</a>
            </div>
            <div class="p-2">
                @foreach($recentUsers as $u)
                    <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                        <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}"
                             class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-slate-800 text-sm truncate">{{ $u->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ $u->email }}</p>
                        </div>
                        <p class="text-xs text-slate-400 flex-shrink-0">{{ $u->created_at->diffForHumans() }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Food Logs --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800"><i class="fa-solid fa-clock-rotate-left mr-1"></i> Aktivitas Terbaru</h3>
            </div>
            <div class="p-2">
                @foreach($recentLogs as $log)
                    <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                        <div class="w-9 h-9 rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm text-slate-500"><i class="fa-solid fa-utensils"></i></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-slate-800 text-sm truncate">{{ $log->food_name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ $log->user->name ?? 'Unknown' }} · {{ $log->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs font-semibold text-primary-600">{{ number_format($log->calories, 0) }} kkal</p>
                            @if($log->ai_detected)
                                <span class="badge-green text-[10px]"><i class="fa-solid fa-wand-magic-sparkles mr-0.5"></i> AI</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
