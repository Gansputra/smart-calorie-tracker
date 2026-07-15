@extends('layouts.app')

@section('title', 'Tambah Makanan Manual')
@section('page-title')
    <i class="fa-solid fa-plus mr-1.5"></i> Tambah Makanan Manual
@endsection
@section('page-subtitle', 'Masukkan data makanan secara manual ke jurnal')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('food-log.store') }}" class="space-y-5" id="manualForm">
            @csrf

            {{-- Food Search from Master --}}
            <div>
                <label class="form-label">Pilih dari Master Makanan (Opsional)</label>
                <select id="foodSearch" class="form-input">
                    <option value="">-- Cari atau Pilih Makanan --</option>
                    @foreach($foods as $food)
                        <option value="{{ $food->id }}"
                                data-name="{{ $food->name }}"
                                data-calories="{{ $food->calories_per_100g }}"
                                data-protein="{{ $food->protein_per_100g }}">
                            {{ $food->name }} — {{ $food->calories_per_100g }} kkal/100g
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="food_id" id="selectedFoodId">
            </div>

            <div class="border-t border-slate-100 pt-5">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Detail Makanan</p>
            </div>

            {{-- Food Name --}}
            <div>
                <label for="food_name" class="form-label">Nama Makanan <span class="text-red-500">*</span></label>
                <input type="text" name="food_name" id="food_name" value="{{ old('food_name') }}"
                       class="form-input @error('food_name') border-red-400 @enderror"
                       placeholder="e.g. Nasi Goreng, Ayam Bakar..." required>
                @error('food_name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Calories & Protein (per 100g) --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="calories" class="form-label">Kalori per 100g (kkal) <span class="text-red-500">*</span></label>
                    <input type="number" name="calories" id="calories" value="{{ old('calories', 0) }}"
                           class="form-input @error('calories') border-red-400 @enderror"
                           step="0.1" min="0" required>
                    @error('calories')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="protein" class="form-label">Protein per 100g (g) <span class="text-red-500">*</span></label>
                    <input type="number" name="protein" id="protein" value="{{ old('protein', 0) }}"
                           class="form-input @error('protein') border-red-400 @enderror"
                           step="0.1" min="0" required>
                    @error('protein')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Portion --}}
            <div>
                <label for="portion" class="form-label">Jumlah Porsi (1 porsi = 100g) <span class="text-red-500">*</span></label>
                <div class="flex gap-2 flex-wrap mb-2">
                    @foreach([0.5, 1, 1.5, 2, 2.5, 3] as $p)
                        <button type="button" data-portion="{{ $p }}"
                                class="portion-btn-manual px-3 py-1.5 text-sm rounded-lg border border-slate-200 text-slate-600 hover:border-primary-500 hover:text-primary-600 {{ $p == 1 ? 'border-primary-500 bg-primary-50 text-primary-600' : '' }}">
                            {{ $p }}x
                        </button>
                    @endforeach
                </div>
                <input type="number" name="portion" id="portion" value="{{ old('portion', 1) }}"
                       step="0.25" min="0.1" max="20"
                       class="form-input @error('portion') border-red-400 @enderror" required>
            </div>

            {{-- Live Preview --}}
            <div class="bg-slate-50 rounded-xl p-4 text-center">
                <p class="text-xs text-slate-500 mb-1">Total yang akan dicatat:</p>
                <p class="font-bold text-slate-800">
                    <span id="totalCal" class="text-xl text-primary-600">0</span> kkal |
                    <span id="totalProt" class="text-xl text-blue-600">0</span>g protein
                </p>
            </div>

            {{-- Meal Type & Date --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="meal_type" class="form-label">Jenis Makan <span class="text-red-500">*</span></label>
                    <select name="meal_type" id="meal_type" class="form-input" required>
                        @foreach($mealTypes as $type)
                            <option value="{{ $type }}" {{ old('meal_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date" class="form-label">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="date" id="date"
                           value="{{ old('date', today()->format('Y-m-d')) }}"
                           class="form-input" required>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label for="notes" class="form-label">Catatan (Opsional)</label>
                <input type="text" name="notes" id="notes" value="{{ old('notes') }}"
                       class="form-input" placeholder="Tambahkan catatan...">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan
                </button>
                <a href="{{ route('food-log.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-fill from master food
document.getElementById('foodSearch').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (option.value) {
        document.getElementById('food_name').value = option.dataset.name;
        document.getElementById('calories').value = option.dataset.calories;
        document.getElementById('protein').value = option.dataset.protein;
        document.getElementById('selectedFoodId').value = option.value;
        updateTotal();
    }
});

// Portion buttons
document.querySelectorAll('.portion-btn-manual').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.portion-btn-manual').forEach(b =>
            b.classList.remove('border-primary-500', 'bg-primary-50', 'text-primary-600'));
        this.classList.add('border-primary-500', 'bg-primary-50', 'text-primary-600');
        document.getElementById('portion').value = this.dataset.portion;
        updateTotal();
    });
});

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

updateTotal();
</script>
@endsection
