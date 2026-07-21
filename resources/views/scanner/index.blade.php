@extends('layouts.app')

@section('title', 'Scan Makanan AI')
@section('page-title')
    <i class="fa-solid fa-camera mr-1.5"></i> Scan Makanan AI
@endsection
@section('page-subtitle', 'Foto makanan Anda dan biarkan AI mendeteksi kalori & protein secara otomatis')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Camera / Upload Panel --}}
    <div class="space-y-4">
        {{-- AI Server Status Banner --}}
        <div class="relative overflow-hidden rounded-2xl p-4 transition-all duration-300 {{ $aiStatus['online'] ? 'bg-gradient-to-r from-emerald-500/10 via-teal-500/5 to-transparent border border-emerald-500/20 shadow-sm shadow-emerald-500/5 dark:from-emerald-950/40 dark:via-emerald-900/20 dark:to-slate-900/60 dark:border-emerald-500/30' : 'bg-gradient-to-r from-rose-500/10 via-red-500/5 to-transparent border border-rose-500/20 shadow-sm shadow-rose-500/5 dark:from-rose-950/40 dark:via-rose-900/20 dark:to-slate-900/60 dark:border-rose-500/30' }}">
            {{-- Top Shimmer Border --}}
            <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r {{ $aiStatus['online'] ? 'from-transparent via-emerald-400/50 to-transparent' : 'from-transparent via-rose-400/50 to-transparent' }}"></div>

            <div class="flex items-center justify-between gap-3 flex-wrap sm:flex-nowrap">
                <div class="flex items-center gap-3.5">
                    {{-- Icon Badge with Glow --}}
                    <div class="relative flex items-center justify-center w-10 h-10 rounded-xl flex-shrink-0 {{ $aiStatus['online'] ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 ring-1 ring-emerald-500/30 shadow-[0_0_12px_rgba(16,185,129,0.2)]' : 'bg-rose-500/15 text-rose-600 dark:text-rose-400 ring-1 ring-rose-500/30 shadow-[0_0_12px_rgba(244,63,94,0.2)]' }}">
                        <i class="fa-solid {{ $aiStatus['online'] ? 'fa-wand-magic-sparkles text-lg animate-pulse' : 'fa-server text-lg' }}"></i>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">
                            AI Neural Server
                        </span>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                            {{ $aiStatus['online'] ? 'Sistem Deteksi Makanan AI Aktif' : 'Server AI Luring (Offline)' }}
                        </p>
                    </div>
                </div>

                {{-- Live Status Indicator Dot & Badge --}}
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full flex-shrink-0 {{ $aiStatus['online'] ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-300' }}">
                    <span class="relative flex h-2.5 w-2.5">
                        @if($aiStatus['online'])
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.9)]"></span>
                        @else
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.9)]"></span>
                        @endif
                    </span>
                    <span class="text-xs font-bold tracking-wide">
                        {{ $aiStatus['online'] ? 'Online • Ready' : 'Offline' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Camera Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Ambil Foto Makanan</h3>
                <p class="text-sm text-slate-500 mt-0.5">Gunakan kamera atau unggah foto dari galeri</p>
            </div>

            <div class="p-5 space-y-4">
                {{-- Camera Preview --}}
                <div class="relative aspect-video bg-slate-900 rounded-xl overflow-hidden transition-all duration-200" id="cameraContainer">
                    <video id="videoPreview" class="w-full h-full object-cover" autoplay playsinline style="display:none;"></video>
                    <canvas id="photoCanvas" class="hidden"></canvas>
                    <img id="photoPreview" class="w-full h-full object-cover" style="display:none;" alt="Preview">

                    {{-- Placeholder --}}
                    <div id="cameraPlaceholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400">
                        <i class="fa-solid fa-camera text-4xl mb-3"></i>
                        <p class="text-sm font-medium">Kamera, upload, atau seret foto ke sini</p>
                        <p class="text-xs mt-1">JPEG, PNG, WebP (maks 10MB)</p>
                    </div>

                    {{-- Drag Overlay Indicator --}}
                    <div id="dragOverlay" class="absolute inset-0 flex flex-col items-center justify-center bg-primary-900/80 text-white border-2 border-dashed border-primary-400 rounded-xl hidden z-10 pointer-events-none" style="display:none;">
                        <i class="fa-solid fa-cloud-arrow-up text-4xl mb-2 animate-bounce"></i>
                        <p class="text-sm font-semibold">Lepaskan gambar di sini</p>
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
                        <i class="fa-solid fa-video text-lg"></i>
                        Buka Kamera
                    </button>
                    <button id="btnCapture" type="button" style="display:none;"
                            class="flex flex-col items-center gap-1.5 px-3 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-medium">
                        <i class="fa-solid fa-camera text-lg"></i>
                        Ambil Foto
                    </button>
                    <label class="flex flex-col items-center gap-1.5 px-3 py-3 bg-slate-50 hover:bg-blue-50 hover:text-blue-700 text-slate-600 rounded-xl text-xs font-medium border border-slate-200 hover:border-blue-200 cursor-pointer">
                        <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                        Upload Foto
                        <input type="file" id="fileInput" accept="image/jpeg,image/jpg,image/png,image/webp" class="hidden">
                    </label>
                    <button id="btnReset" type="button" style="display:none;"
                            class="flex flex-col items-center gap-1.5 px-3 py-3 bg-slate-50 hover:bg-red-50 hover:text-red-600 text-slate-500 rounded-xl text-xs font-medium border border-slate-200">
                        <i class="fa-solid fa-arrow-rotate-left text-lg"></i>
                        Reset
                    </button>
                </div>

                {{-- Analyze Button --}}
                <button id="btnAnalyze" type="button"
                        class="btn-primary w-full"
                        disabled
                        style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fa-solid fa-wand-magic-sparkles mr-1.5"></i>
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
                    <span class="text-primary-600"><i class="fa-solid fa-crosshairs"></i></span>
                    <h3 class="font-bold text-slate-800">Hasil Deteksi AI</h3>
                </div>
            </div>

            <div class="p-5 space-y-4">
                {{-- Food Name --}}
                <div class="text-center py-4">
                    <p class="text-3xl mb-1 text-slate-600"><i class="fa-solid fa-utensils"></i></p>
                    <h4 id="foodName" class="text-xl font-bold text-slate-800"></h4>
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
                        <i class="fa-solid fa-floppy-disk mr-1.5"></i>
                        Simpan ke Jurnal
                    </button>
                </form>
            </div>
        </div>

        {{-- Instructions (shown when no result) --}}
        <div id="instructionsPanel" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-bold text-slate-800 mb-4"><i class="fa-solid fa-clipboard-list mr-1"></i> Cara Menggunakan</h3>
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
                    <i class="fa-solid fa-lightbulb text-amber-500 mr-1"></i> <strong>Tips:</strong> Foto makanan dari sudut atas dengan pencahayaan yang baik untuk hasil terbaik. Pastikan seluruh makanan terlihat jelas.
                </p>
            </div>
        </div>

        {{-- Error Panel --}}
        <div id="errorPanel" class="bg-red-50 border border-red-200 rounded-2xl p-5" style="display:none;">
            <div class="flex gap-3">
                <i class="fa-solid fa-circle-xmark text-red-500 text-lg flex-shrink-0 mt-0.5"></i>
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

const cameraContainer = document.getElementById('cameraContainer');
const dragOverlay = document.getElementById('dragOverlay');

function showElement(el) { el.style.display = ''; }
function hideElement(el) { el.style.display = 'none'; }

function handleFile(file) {
    if (!file || !file.type.startsWith('image/')) return;
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
}

// Drag & Drop
let dragCounter = 0;

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    cameraContainer.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
    });
});

cameraContainer.addEventListener('dragenter', () => {
    dragCounter++;
    cameraContainer.classList.add('ring-4', 'ring-primary-500/50', 'border-2', 'border-primary-500');
    showElement(dragOverlay);
});

cameraContainer.addEventListener('dragleave', () => {
    dragCounter--;
    if (dragCounter <= 0) {
        dragCounter = 0;
        cameraContainer.classList.remove('ring-4', 'ring-primary-500/50', 'border-2', 'border-primary-500');
        hideElement(dragOverlay);
    }
});

cameraContainer.addEventListener('drop', (e) => {
    dragCounter = 0;
    cameraContainer.classList.remove('ring-4', 'ring-primary-500/50', 'border-2', 'border-primary-500');
    hideElement(dragOverlay);
    const files = e.dataTransfer ? e.dataTransfer.files : null;
    if (files && files.length > 0) {
        handleFile(files[0]);
    }
});

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
    if (file) handleFile(file);
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
            hideElement(document.getElementById('resultPanel'));
            showElement(document.getElementById('instructionsPanel'));
        }
    } catch (e) {
        document.getElementById('errorMessage').textContent = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
        showElement(document.getElementById('errorPanel'));
        hideElement(document.getElementById('resultPanel'));
        showElement(document.getElementById('instructionsPanel'));
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
