@extends('layouts.app')

@section('title', 'Scan Makanan AI')
@section('page-title', '📸 Scan Makanan AI')
@section('page-subtitle', 'Foto makanan Anda dan biarkan AI mendeteksi kalori & protein secara otomatis')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Camera / Upload Panel --}}
    <div class="space-y-4">
        {{-- AI Status --}}
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl {{ $aiStatus['online'] ? 'bg-primary-50 border border-primary-200' : 'bg-red-50 border border-red-200' }}">
            <div class="w-2.5 h-2.5 rounded-full {{ $aiStatus['online'] ? 'bg-primary-500 animate-pulse' : 'bg-red-500' }}"></div>
            <span class="text-sm font-medium {{ $aiStatus['online'] ? 'text-primary-700' : 'text-red-700' }}">
                AI Server: {{ $aiStatus['online'] ? 'Online ✓' : 'Offline — AI Server tidak berjalan' }}
            </span>
        </div>

        {{-- Camera Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Ambil Foto Makanan</h3>
                <p class="text-sm text-slate-500 mt-0.5">Gunakan kamera atau unggah foto dari galeri</p>
            </div>

            <div class="p-5 space-y-4">
                {{-- Camera Preview --}}
                <div class="relative aspect-video bg-slate-900 rounded-xl overflow-hidden" id="cameraContainer">
                    <video id="videoPreview" class="w-full h-full object-cover" autoplay playsinline style="display:none;"></video>
                    <canvas id="photoCanvas" class="hidden"></canvas>
                    <img id="photoPreview" class="w-full h-full object-cover" style="display:none;" alt="Preview">

                    {{-- Placeholder --}}
                    <div id="cameraPlaceholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400">
                        <svg class="w-16 h-16 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="text-sm font-medium">Kamera atau upload foto</p>
                        <p class="text-xs mt-1">JPEG, PNG, WebP (maks 10MB)</p>
                    </div>

                    {{-- Scan overlay --}}
                    <div id="scanOverlay" class="absolute inset-0 items-center justify-center bg-black/60 hidden" style="display:none;">
                        <div class="text-center text-white">
                            <div class="w-16 h-16 border-4 border-primary-400 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                            <p class="font-semibold">AI sedang menganalisis...</p>
                            <p class="text-sm text-slate-300 mt-1">Mohon tunggu sebentar</p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="grid grid-cols-3 gap-2">
                    <button id="btnCamera" type="button"
                            class="flex flex-col items-center gap-1.5 px-3 py-3 bg-slate-50 hover:bg-primary-50 hover:text-primary-700 text-slate-600 rounded-xl text-xs font-medium border border-slate-200 hover:border-primary-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
                        </svg>
                        Buka Kamera
                    </button>
                    <button id="btnCapture" type="button" style="display:none;"
                            class="flex flex-col items-center gap-1.5 px-3 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-medium">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Ambil Foto
                    </button>
                    <label class="flex flex-col items-center gap-1.5 px-3 py-3 bg-slate-50 hover:bg-blue-50 hover:text-blue-700 text-slate-600 rounded-xl text-xs font-medium border border-slate-200 hover:border-blue-200 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Upload Foto
                        <input type="file" id="fileInput" accept="image/jpeg,image/jpg,image/png,image/webp" class="hidden">
                    </label>
                    <button id="btnReset" type="button" style="display:none;"
                            class="flex flex-col items-center gap-1.5 px-3 py-3 bg-slate-50 hover:bg-red-50 hover:text-red-600 text-slate-500 rounded-xl text-xs font-medium border border-slate-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </button>
                </div>

                {{-- Analyze Button --}}
                <button id="btnAnalyze" type="button"
                        class="btn-primary w-full"
                        disabled
                        style="opacity: 0.5; cursor: not-allowed;">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    Analisis dengan AI
                </button>
            </div>
        </div>
    </div>

    {{-- Result Panel --}}
    <div class="space-y-4">
        {{-- Prediction Result (hidden by default) --}}
        <div id="resultPanel" class="bg-white rounded-2xl shadow-sm border border-slate-100" style="display:none;">
            <div class="p-5 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🎯</span>
                    <h3 class="font-bold text-slate-800">Hasil Deteksi AI</h3>
                    <span id="confidenceBadge" class="ml-auto badge-green text-xs"></span>
                </div>
            </div>

            <div class="p-5 space-y-4">
                {{-- Food Name --}}
                <div class="text-center py-4">
                    <p class="text-3xl mb-1">🍽️</p>
                    <h4 id="foodName" class="text-xl font-bold text-slate-800"></h4>
                    <p id="confidenceText" class="text-sm text-slate-500 mt-1"></p>
                </div>

                {{-- Nutrition per 100g --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-primary-50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-primary-700" id="caloriesDisplay"></p>
                        <p class="text-xs text-primary-600 font-medium">kkal per porsi</p>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-blue-700" id="proteinDisplay"></p>
                        <p class="text-xs text-blue-600 font-medium">g protein per porsi</p>
                    </div>
                </div>

                {{-- Save Form --}}
                <form method="POST" action="{{ route('scanner.save') }}" id="saveForm" class="space-y-3 pt-2 border-t border-slate-100">
                    @csrf
                    <input type="hidden" name="food_name" id="hiddenFoodName">
                    <input type="hidden" name="calories" id="hiddenCalories">
                    <input type="hidden" name="protein" id="hiddenProtein">
                    <input type="hidden" name="confidence" id="hiddenConfidence">

                    {{-- Portion --}}
                    <div>
                        <label class="form-label">Jumlah Porsi (1 porsi = 100g)</label>
                        <div class="flex gap-2 flex-wrap mb-2">
                            @foreach([0.5, 1, 1.5, 2, 2.5] as $p)
                                <button type="button" data-portion="{{ $p }}"
                                        class="portion-btn px-3 py-1.5 text-sm rounded-lg border border-slate-200 text-slate-600 hover:border-primary-500 hover:text-primary-600 hover:bg-primary-50 {{ $p == 1 ? 'border-primary-500 text-primary-600 bg-primary-50' : '' }}">
                                    {{ $p }}x
                                </button>
                            @endforeach
                        </div>
                        <input type="number" name="portion" id="portionInput" value="1" step="0.25" min="0.25" max="20"
                               class="form-input" placeholder="Atau ketik jumlah porsi">
                    </div>

                    {{-- Live calculation --}}
                    <div class="bg-slate-50 rounded-xl p-3 text-center">
                        <p class="text-sm text-slate-500">Total yang akan disimpan:</p>
                        <p class="font-bold text-slate-800">
                            <span id="totalCalories" class="text-primary-600">0</span> kkal |
                            <span id="totalProtein" class="text-blue-600">0</span>g protein
                        </p>
                    </div>

                    {{-- Meal Type --}}
                    <div>
                        <label class="form-label">Jenis Makan</label>
                        <select name="meal_type" class="form-input">
                            @foreach($mealTypes as $type)
                                <option value="{{ $type }}" {{ $loop->index === 0 ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="form-label">Catatan (Opsional)</label>
                        <input type="text" name="notes" class="form-input" placeholder="Tambahkan catatan...">
                    </div>

                    <button type="submit" class="btn-primary w-full">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan ke Jurnal
                    </button>
                </form>
            </div>
        </div>

        {{-- Instructions (shown when no result) --}}
        <div id="instructionsPanel" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-bold text-slate-800 mb-4">📋 Cara Menggunakan</h3>
            <ol class="space-y-3">
                @foreach([
                    ['Buka kamera atau upload foto makanan Anda', '1'],
                    ['Klik tombol "Analisis dengan AI"', '2'],
                    ['AI akan mendeteksi nama makanan, kalori, dan protein', '3'],
                    ['Atur jumlah porsi sesuai yang Anda makan', '4'],
                    ['Simpan ke jurnal makanan', '5'],
                ] as $step)
                    <li class="flex gap-3 items-start">
                        <span class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">{{ $step[1] }}</span>
                        <span class="text-sm text-slate-600">{{ $step[0] }}</span>
                    </li>
                @endforeach
            </ol>

            <div class="mt-5 p-3 bg-amber-50 rounded-xl border border-amber-200">
                <p class="text-xs text-amber-700">
                    💡 <strong>Tips:</strong> Foto makanan dari sudut atas dengan pencahayaan yang baik untuk hasil terbaik. Pastikan seluruh makanan terlihat jelas.
                </p>
            </div>
        </div>

        {{-- Error Panel --}}
        <div id="errorPanel" class="bg-red-50 border border-red-200 rounded-2xl p-5" style="display:none;">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="font-semibold text-red-800">Prediksi Gagal</p>
                    <p id="errorMessage" class="text-sm text-red-700 mt-1"></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
let capturedBlob = null;
let baseCalories = 0;
let baseProtein = 0;
let stream = null;

const video = document.getElementById('videoPreview');
const photoCanvas = document.getElementById('photoCanvas');
const photoPreview = document.getElementById('photoPreview');
const placeholder = document.getElementById('cameraPlaceholder');
const scanOverlay = document.getElementById('scanOverlay');
const btnCamera = document.getElementById('btnCamera');
const btnCapture = document.getElementById('btnCapture');
const btnReset = document.getElementById('btnReset');
const btnAnalyze = document.getElementById('btnAnalyze');
const fileInput = document.getElementById('fileInput');

function showElement(el) { el.style.display = ''; }
function hideElement(el) { el.style.display = 'none'; }

// Open camera
btnCamera.addEventListener('click', async () => {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
        video.srcObject = stream;
        showElement(video);
        hideElement(placeholder);
        hideElement(photoPreview);
        showElement(btnCapture);
        hideElement(btnCamera);
        showElement(btnReset);
    } catch(e) {
        alert('Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.');
    }
});

// Capture photo
btnCapture.addEventListener('click', () => {
    photoCanvas.width = video.videoWidth;
    photoCanvas.height = video.videoHeight;
    photoCanvas.getContext('2d').drawImage(video, 0, 0);
    photoCanvas.toBlob(blob => {
        capturedBlob = blob;
        photoPreview.src = URL.createObjectURL(blob);
        showElement(photoPreview);
        hideElement(video);
        if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
        hideElement(btnCapture);
        enableAnalyze();
    }, 'image/jpeg', 0.92);
});

// File upload
fileInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;
    capturedBlob = file;
    const reader = new FileReader();
    reader.onload = (ev) => {
        photoPreview.src = ev.target.result;
        showElement(photoPreview);
        hideElement(video);
        hideElement(placeholder);
        if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
        showElement(btnReset);
        hideElement(btnCamera);
        hideElement(btnCapture);
        enableAnalyze();
    };
    reader.readAsDataURL(file);
});

// Reset
btnReset.addEventListener('click', () => {
    capturedBlob = null;
    if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
    hideElement(video);
    hideElement(photoPreview);
    showElement(placeholder);
    showElement(btnCamera);
    hideElement(btnCapture);
    hideElement(btnReset);
    disableAnalyze();
    hideElement(document.getElementById('resultPanel'));
    showElement(document.getElementById('instructionsPanel'));
    hideElement(document.getElementById('errorPanel'));
    fileInput.value = '';
});

function enableAnalyze() {
    btnAnalyze.disabled = false;
    btnAnalyze.style.opacity = '1';
    btnAnalyze.style.cursor = 'pointer';
}
function disableAnalyze() {
    btnAnalyze.disabled = true;
    btnAnalyze.style.opacity = '0.5';
    btnAnalyze.style.cursor = 'not-allowed';
}

// Analyze with AI
btnAnalyze.addEventListener('click', async () => {
    if (!capturedBlob) return;

    scanOverlay.style.display = 'flex';
    btnAnalyze.disabled = true;
    hideElement(document.getElementById('errorPanel'));

    const formData = new FormData();
    formData.append('image', capturedBlob, 'food.jpg');
    formData.append('_token', CSRF_TOKEN);

    try {
        const response = await fetch('{{ route("scanner.predict") }}', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Show result
            baseCalories = parseFloat(data.calories);
            baseProtein = parseFloat(data.protein);

            document.getElementById('foodName').textContent = data.food_name;
            document.getElementById('confidenceBadge').textContent = `${(data.confidence * 100).toFixed(1)}% yakin`;
            document.getElementById('confidenceText').textContent = `Tingkat keyakinan AI: ${(data.confidence * 100).toFixed(1)}%`;

            document.getElementById('hiddenFoodName').value = data.food_name;
            document.getElementById('hiddenCalories').value = data.calories;
            document.getElementById('hiddenProtein').value = data.protein;
            document.getElementById('hiddenConfidence').value = data.confidence;

            updateCalcDisplay();

            hideElement(document.getElementById('instructionsPanel'));
            showElement(document.getElementById('resultPanel'));
        } else {
            document.getElementById('errorMessage').textContent = data.message || 'Terjadi kesalahan.';
            showElement(document.getElementById('errorPanel'));
        }
    } catch (e) {
        document.getElementById('errorMessage').textContent = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
        showElement(document.getElementById('errorPanel'));
    } finally {
        hideElement(scanOverlay);
        enableAnalyze();
    }
});

// Portion selector
document.querySelectorAll('.portion-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.portion-btn').forEach(b => b.classList.remove('border-primary-500', 'text-primary-600', 'bg-primary-50'));
        btn.classList.add('border-primary-500', 'text-primary-600', 'bg-primary-50');
        document.getElementById('portionInput').value = btn.dataset.portion;
        updateCalcDisplay();
    });
});

document.getElementById('portionInput').addEventListener('input', updateCalcDisplay);

function updateCalcDisplay() {
    const portion = parseFloat(document.getElementById('portionInput').value) || 1;
    document.getElementById('caloriesDisplay').textContent = Math.round(baseCalories * portion);
    document.getElementById('proteinDisplay').textContent = (baseProtein * portion).toFixed(1);
    document.getElementById('totalCalories').textContent = Math.round(baseCalories * portion);
    document.getElementById('totalProtein').textContent = (baseProtein * portion).toFixed(1);
}
</script>
@endpush
