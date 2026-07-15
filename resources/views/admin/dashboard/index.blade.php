@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', '⚙️ Dashboard Admin')
@section('page-subtitle', 'Ringkasan dan statistik aplikasi Smart Calorie Tracker')

@section('content')
<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl stat-gradient-blue flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-800">{{ $totalUsers }}</p>
            <p class="text-slate-500 text-sm mt-1">Total User</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl stat-gradient-green flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-800">{{ number_format($totalFoodLogs) }}</p>
            <p class="text-slate-500 text-sm mt-1">Total Food Logs</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl stat-gradient-orange flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-800">{{ $totalFoods }}</p>
            <p class="text-slate-500 text-sm mt-1">Master Makanan</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl stat-gradient-purple flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
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
                <h3 class="font-bold text-slate-800">👥 User Terbaru</h3>
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
                <h3 class="font-bold text-slate-800">🍽️ Aktivitas Terbaru</h3>
            </div>
            <div class="p-2">
                @foreach($recentLogs as $log)
                    <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                        <div class="w-9 h-9 rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm">🍴</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-slate-800 text-sm truncate">{{ $log->food_name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ $log->user->name ?? 'Unknown' }} · {{ $log->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs font-semibold text-primary-600">{{ number_format($log->calories, 0) }} kkal</p>
                            @if($log->ai_detected)
                                <span class="badge-green text-[10px]">AI</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
