@extends('layouts.admin')

@section('title', 'Master Makanan')
@section('page-title', '🍱 Master Makanan')
@section('page-subtitle', 'Kelola database makanan untuk seluruh pengguna')

@section('content')
<div class="space-y-5">
    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-between">
        <form action="{{ route('admin.foods.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-input max-w-xs" placeholder="Cari makanan...">
            <select name="category" class="form-input max-w-40">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary btn-sm">Cari</button>
        </form>
        <a href="{{ route('admin.foods.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Makanan
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
        <div class="table-container rounded-2xl">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Makanan</th>
                        <th>Kategori</th>
                        <th>Kalori/100g</th>
                        <th>Protein/100g</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($foods as $food)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    @if($food->image)
                                        <img src="{{ $food->image_url }}" alt="{{ $food->name }}"
                                             class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                            <span class="text-lg">🍽️</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $food->name }}</p>
                                        @if($food->description)
                                            <p class="text-xs text-slate-400 truncate max-w-48">{{ Str::limit($food->description, 50) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-slate-100 text-slate-600">{{ $food->category }}</span></td>
                            <td class="font-semibold text-primary-600">{{ $food->calories_per_100g }}</td>
                            <td class="font-semibold text-blue-600">{{ $food->protein_per_100g }}g</td>
                            <td>
                                @if($food->is_active)
                                    <span class="badge-green">Aktif</span>
                                @else
                                    <span class="badge bg-red-100 text-red-600">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.foods.edit', $food) }}"
                                       class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.foods.destroy', $food) }}"
                                          onsubmit="return confirm('Hapus {{ $food->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                Belum ada data makanan. <a href="{{ route('admin.foods.create') }}" class="text-primary-600 hover:underline">Tambah sekarang →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($foods->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $foods->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
