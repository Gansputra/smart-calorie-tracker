@extends('layouts.app')

@section('title', 'Fat Loss Tracker')
@section('page-title', '⚖️ Fat Loss Tracker')
@section('page-subtitle', 'Pantau perkembangan berat badan Anda')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Chart & Stats --}}
    <div class="lg:col-span-2 space-y-4">
        {{-- Quick Stats --}}
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm text-center">
                <p class="text-xs text-slate-500 mb-1">Berat Terkini</p>
                @if($latestWeight)
                    <p class="text-2xl font-bold text-slate-800">{{ $latestWeight->weight }}<span class="text-sm text-slate-400">kg</span></p>
                    <p class="text-xs text-slate-400 mt-1">{{ $latestWeight->date->format('d/m/Y') }}</p>
                @else
                    <p class="text-lg text-slate-400 font-medium">—</p>
                @endif
            </div>
            <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm text-center">
                <p class="text-xs text-slate-500 mb-1">Perubahan</p>
                @if($weightChange !== null)
                    <p class="text-2xl font-bold {{ $weightChange < 0 ? 'text-green-600' : ($weightChange > 0 ? 'text-red-500' : 'text-slate-600') }}">
                        {{ $weightChange > 0 ? '+' : '' }}{{ $weightChange }}<span class="text-sm">kg</span>
                    </p>
                    <p class="text-xs text-slate-400 mt-1">{{ $weightChange < 0 ? '📉 Turun' : ($weightChange > 0 ? '📈 Naik' : '➡️ Stabil') }}</p>
                @else
                    <p class="text-lg text-slate-400 font-medium">—</p>
                @endif
            </div>
            <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm text-center">
                <p class="text-xs text-slate-500 mb-1">Total Entri</p>
                <p class="text-2xl font-bold text-slate-800">{{ $weightLogs->count() }}</p>
                <p class="text-xs text-slate-400 mt-1">catatan</p>
            </div>
        </div>

        {{-- Chart --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800">📈 Grafik Berat Badan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Perkembangan berat badan dari waktu ke waktu</p>
                </div>
                <div class="flex gap-1">
                    @foreach([['30', '30H'], ['60', '60H'], ['90', '90H']] as $p)
                        <a href="{{ route('weight-log.index', ['period' => $p[0]]) }}"
                           class="px-2.5 py-1 text-xs rounded-lg font-medium {{ $period == $p[0] ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $p[1] }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="p-5">
                @if(count($chartLabels) > 0)
                    <canvas id="weightChart" height="220"></canvas>
                @else
                    <div class="h-48 flex items-center justify-center text-slate-400 text-sm">
                        Belum ada data untuk ditampilkan
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Add & Table Panel --}}
    <div class="space-y-4">
        {{-- Add Weight Form --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <h3 class="font-bold text-slate-800 mb-4">➕ Catat Berat Badan</h3>
            <form method="POST" action="{{ route('weight-log.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">Berat Badan (kg)</label>
                    <input type="number" name="weight" step="0.1" min="20" max="500"
                           value="{{ old('weight') }}"
                           class="form-input @error('weight') border-red-400 @enderror"
                           placeholder="e.g. 70.5" required>
                    @error('weight')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" value="{{ old('date', today()->format('Y-m-d')) }}"
                           class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Catatan (Opsional)</label>
                    <input type="text" name="notes" value="{{ old('notes') }}"
                           class="form-input" placeholder="e.g. Setelah olahraga">
                </div>
                <button type="submit" class="btn-primary w-full">⚖️ Simpan</button>
            </form>
        </div>

        {{-- Weight Log Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Riwayat</h3>
            </div>
            <div class="max-h-80 overflow-y-auto">
                @if($weightLogs->count() > 0)
                    @foreach($weightLogs as $log)
                        <div class="px-5 py-3 border-b border-slate-50 flex items-center justify-between last:border-0">
                            <div>
                                <p class="font-semibold text-slate-800">{{ number_format($log->weight, 1) }} kg</p>
                                <p class="text-xs text-slate-400">{{ $log->date->format('d M Y') }}</p>
                                @if($log->notes)
                                    <p class="text-xs text-slate-400 italic">{{ $log->notes }}</p>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('weight-log.destroy', $log) }}"
                                  onsubmit="return confirm('Hapus catatan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                @else
                    <div class="p-8 text-center text-slate-400 text-sm">
                        Belum ada catatan berat badan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if(count($chartLabels) > 0)
const ctx = document.getElementById('weightChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Berat Badan (kg)',
            data: @json($chartWeights),
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            borderColor: '#16a34a',
            borderWidth: 3,
            pointBackgroundColor: '#16a34a',
            pointRadius: 5,
            pointHoverRadius: 7,
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0f1923',
                titleColor: '#fff',
                bodyColor: '#cbd5e1',
                padding: 12,
                cornerRadius: 10,
                callbacks: {
                    label: ctx => `${ctx.parsed.y} kg`
                }
            }
        },
        scales: {
            y: {
                grid: { color: '#f1f5f9' },
                ticks: { color: '#64748b', font: { size: 11 }, callback: v => v + ' kg' }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#64748b', font: { size: 11 } }
            }
        }
    }
});
@endif
</script>
@endpush
