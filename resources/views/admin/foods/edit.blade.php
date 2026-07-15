@extends('layouts.admin')

@section('title', 'Edit Makanan')
@section('page-title', '✏️ Edit Makanan')
@section('page-subtitle', 'Perbarui informasi makanan: ' . $food->name)

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('admin.foods.update', $food) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="name" class="form-label">Nama Makanan *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $food->name) }}"
                           class="form-input" required>
                </div>

                <div>
                    <label for="category" class="form-label">Kategori</label>
                    <select name="category" id="category" class="form-input" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category', $food->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-3 pt-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $food->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-slate-300 text-primary-600">
                        <span class="text-sm font-medium text-slate-700">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-sm font-semibold text-slate-600 mb-3">🥗 Nilai Gizi per 100g</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label text-xs">Kalori (kkal) *</label>
                        <input type="number" name="calories_per_100g" value="{{ old('calories_per_100g', $food->calories_per_100g) }}"
                               class="form-input" step="0.1" min="0" required>
                    </div>
                    <div>
                        <label class="form-label text-xs">Protein (g) *</label>
                        <input type="number" name="protein_per_100g" value="{{ old('protein_per_100g', $food->protein_per_100g) }}"
                               class="form-input" step="0.1" min="0" required>
                    </div>
                    <div>
                        <label class="form-label text-xs">Karbohidrat (g)</label>
                        <input type="number" name="carbs_per_100g" value="{{ old('carbs_per_100g', $food->carbs_per_100g) }}"
                               class="form-input" step="0.1" min="0">
                    </div>
                    <div>
                        <label class="form-label text-xs">Lemak (g)</label>
                        <input type="number" name="fat_per_100g" value="{{ old('fat_per_100g', $food->fat_per_100g) }}"
                               class="form-input" step="0.1" min="0">
                    </div>
                </div>
            </div>

            <div>
                <label class="form-label">Deskripsi</label>
                <textarea name="description" rows="3" class="form-input resize-none"
                          placeholder="Deskripsi singkat...">{{ old('description', $food->description) }}</textarea>
            </div>

            <div>
                <label class="form-label">Gambar Makanan</label>
                @if($food->image)
                    <div class="flex items-center gap-3 mb-3">
                        <img src="{{ $food->image_url }}" alt="{{ $food->name }}"
                             id="imagePreview" class="h-20 w-20 rounded-xl object-cover">
                        <p class="text-xs text-slate-400">Gambar saat ini. Upload baru untuk mengganti.</p>
                    </div>
                @else
                    <img id="imagePreview" class="h-20 w-20 rounded-xl object-cover mb-3 hidden">
                @endif
                <input type="file" name="image" accept="image/*" class="form-input"
                       onchange="previewImage(this)">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1">Simpan Perubahan</button>
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
