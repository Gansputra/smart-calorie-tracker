@extends('layouts.admin')

@section('title', 'Master Makanan')
@section('page-title')
    <i class="fa-solid fa-pizza-slice mr-1.5"></i> Master Makanan
@endsection
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
            <i class="fa-solid fa-plus mr-1"></i>
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
                                            <i class="fa-solid fa-utensils text-slate-500"></i>
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
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.foods.destroy', $food) }}"
                                          onsubmit="return confirm('Hapus {{ $food->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                            <i class="fa-solid fa-trash"></i>
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
