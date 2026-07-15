@extends('layouts.app')

@section('title', 'Edit Catatan Makanan')
@section('page-title')
    <i class="fa-solid fa-pen-to-square mr-1.5"></i> Edit Catatan Makanan
@endsection
@section('page-subtitle', 'Perbarui informasi catatan makanan Anda')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('food-log.update', $foodLog) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="food_name" class="form-label">Nama Makanan <span class="text-red-500">*</span></label>
                <input type="text" name="food_name" id="food_name"
                       value="{{ old('food_name', $foodLog->food_name) }}"
                       class="form-input @error('food_name') border-red-400 @enderror" required>
                @error('food_name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="calories" class="form-label">Kalori per 100g (kkal)</label>
                    <input type="number" name="calories" id="calories"
                           value="{{ old('calories', $foodLog->calories / $foodLog->portion) }}"
                           class="form-input" step="0.1" min="0" required>
                </div>
                <div>
                    <label for="protein" class="form-label">Protein per 100g (g)</label>
                    <input type="number" name="protein" id="protein"
                           value="{{ old('protein', $foodLog->protein / $foodLog->portion) }}"
                           class="form-input" step="0.1" min="0" required>
                </div>
            </div>

            <div>
                <label for="portion" class="form-label">Jumlah Porsi (1 porsi = 100g)</label>
                <input type="number" name="portion" id="portion"
                       value="{{ old('portion', $foodLog->portion) }}"
                       class="form-input" step="0.25" min="0.1" max="20" required>
            </div>

            <div class="bg-slate-50 rounded-xl p-4 text-center">
                <p class="text-xs text-slate-500 mb-1">Total setelah diperbarui:</p>
                <p class="font-bold text-slate-800">
                    <span id="totalCal" class="text-xl text-primary-600">{{ number_format($foodLog->calories, 0) }}</span> kkal |
                    <span id="totalProt" class="text-xl text-blue-600">{{ number_format($foodLog->protein, 1) }}</span>g protein
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="meal_type" class="form-label">Jenis Makan</label>
                    <select name="meal_type" id="meal_type" class="form-input" required>
                        @foreach($mealTypes as $type)
                            <option value="{{ $type }}" {{ old('meal_type', $foodLog->meal_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date" class="form-label">Tanggal</label>
                    <input type="date" name="date" id="date"
                           value="{{ old('date', $foodLog->date->format('Y-m-d')) }}"
                           class="form-input" required>
                </div>
            </div>

            <div>
                <label for="notes" class="form-label">Catatan</label>
                <input type="text" name="notes" id="notes"
                       value="{{ old('notes', $foodLog->notes) }}"
                       class="form-input" placeholder="Tambahkan catatan...">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Perubahan
                </button>
                <a href="{{ route('food-log.index', ['date' => $foodLog->date->format('Y-m-d')]) }}"
                   class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
['calories', 'protein', 'portion'].forEach(id => {
    document.getElementById(id).addEventListener('input', updateTotal);
});
function updateTotal() {
    const cal = parseFloat(document.getElementById('calories').value) || 0;
    const prot = parseFloat(document.getElementById('protein').value) || 0;
    const portion = parseFloat(document.getElementById('portion').value) || 1;
    document.getElementById('totalCal').textContent = Math.round(cal * portion);
    document.getElementById('totalProt').textContent = (prot * portion).toFixed(1);
}
</script>
@endsection
