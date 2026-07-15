@extends('layouts.admin')

@section('title', 'Manajemen User')
@section('page-title', '👥 Manajemen User')
@section('page-subtitle', 'Lihat dan kelola semua pengguna terdaftar')

@section('content')
<div class="space-y-5">
    {{-- Stats & Search --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-between items-start">
        <div class="flex gap-3">
            <div class="bg-white rounded-xl px-4 py-3 border border-slate-100 shadow-sm">
                <p class="text-xs text-slate-500">Total User</p>
                <p class="text-xl font-bold text-slate-800">{{ $totalUsers }}</p>
            </div>
        </div>

        <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-input max-w-64" placeholder="Cari nama atau email...">
            <button type="submit" class="btn-secondary btn-sm">Cari</button>
            @if(request('search'))
                <a href="{{ route('admin.users.index') }}" class="btn-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
        <div class="table-container rounded-2xl">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Target Kalori</th>
                        <th>Food Logs</th>
                        <th>Weight Logs</th>
                        <th>Bergabung</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}"
                                         class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $u->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-sm text-primary-600 font-semibold">{{ number_format($u->calorie_target) }} kkal</p>
                                <p class="text-xs text-slate-400">{{ $u->protein_target }}g protein</p>
                            </td>
                            <td>
                                <span class="badge-green text-xs font-bold">{{ $u->food_logs_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-blue-100 text-blue-700 text-xs font-bold">{{ $u->weight_logs_count }}</span>
                            </td>
                            <td class="text-slate-500 text-sm">{{ $u->created_at->format('d M Y') }}</td>
                            <td class="text-right">
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                      onsubmit="return confirm('Hapus user {{ $u->name }}? Data akan hilang permanen.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                Tidak ada user yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
