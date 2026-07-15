@extends('layouts.admin')

@section('title', 'Tambah Makanan')
@section('page-title', '➕ Tambah Makanan Baru')
@section('page-subtitle', 'Tambahkan makanan baru ke database master')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('admin.foods.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="name" class="form-label">Nama Makanan <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           class="form-input @error('name') border-red-400 @enderror"
                           placeholder="e.g. Nasi Goreng" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="form-label">Kategori <span class="text-red-500">*</span></label>
                    <select name="category" id="category" class="form-input" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-3 pt-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-slate-300 text-primary-600">
                        <span class="text-sm font-medium text-slate-700">Aktif (tampil ke user)</span>
                    </label>
                </div>
            </div>

            {{-- Nutrition per 100g --}}
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-sm font-semibold text-slate-600 mb-3">🥗 Nilai Gizi per 100g</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="calories_per_100g" class="form-label text-xs">Kalori (kkal) *</label>
                        <input type="number" name="calories_per_100g" id="calories_per_100g"
                               value="{{ old('calories_per_100g', 0) }}"
                               class="form-input @error('calories_per_100g') border-red-400 @enderror"
                               step="0.1" min="0" required>
                    </div>
                    <div>
                        <label for="protein_per_100g" class="form-label text-xs">Protein (g) *</label>
                        <input type="number" name="protein_per_100g" id="protein_per_100g"
                               value="{{ old('protein_per_100g', 0) }}"
                               class="form-input @error('protein_per_100g') border-red-400 @enderror"
                               step="0.1" min="0" required>
                    </div>
                    <div>
                        <label for="carbs_per_100g" class="form-label text-xs">Karbohidrat (g)</label>
                        <input type="number" name="carbs_per_100g" id="carbs_per_100g"
                               value="{{ old('carbs_per_100g') }}"
                               class="form-input" step="0.1" min="0">
                    </div>
                    <div>
                        <label for="fat_per_100g" class="form-label text-xs">Lemak (g)</label>
                        <input type="number" name="fat_per_100g" id="fat_per_100g"
                               value="{{ old('fat_per_100g') }}"
                               class="form-input" step="0.1" min="0">
                    </div>
                </div>
            </div>

            <div>
                <label for="description" class="form-label">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                          class="form-input resize-none"
                          placeholder="Deskripsi singkat makanan...">{{ old('description') }}</textarea>
            </div>

            <div>
                <label for="image" class="form-label">Gambar Makanan</label>
                <input type="file" name="image" id="image" accept="image/*"
                       class="form-input" onchange="previewImage(this)">
                <img id="imagePreview" class="mt-3 h-32 w-32 rounded-xl object-cover hidden">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Makanan
                </button>
                <a href="{{ route('admin.foods.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.classList.remove('hidden'); };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
