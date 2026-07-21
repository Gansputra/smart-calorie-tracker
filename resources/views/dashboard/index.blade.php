@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name . '! Ini ringkasan hari ini.')

@section('content')
    <div class="space-y-6">

        {{-- Date Navigator --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard', ['date' => \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d')]) }}"
                    class="p-2 rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </a>
                <div class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-700">
                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    @if($date === today()->format('Y-m-d'))
                        <span class="ml-2 badge-green text-[10px]">Hari Ini</span>
                    @endif
                </div>
                @if($date !== today()->format('Y-m-d'))
                    <a href="{{ route('dashboard', ['date' => \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d')]) }}"
                        class="p-2 rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                @endif
            </div>

            <div class="flex gap-2">
                <a href="{{ route('scanner.index') }}" class="btn-primary btn-sm text-sm">
                    <i class="fa-solid fa-camera"></i>
                    Scan AI
                </a>
                <a href="{{ route('food-log.create') }}" class="btn-secondary btn-sm text-sm">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Manual
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Calories --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl stat-gradient-green flex items-center justify-center">
                        <i class="fa-solid fa-fire text-white text-lg"></i>
                    </div>
                    <span class="text-2xl font-bold text-primary-600">{{ $calorie_percentage }}%</span>
                </div>
                <p class="text-2xl font-bold text-slate-800">{{ number_format($total_calories, 0) }}</p>
                <p class="text-slate-500 text-sm">dari {{ number_format($calorie_target, 0) }} kkal</p>
                <div class="mt-3 h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="progress-bar h-full rounded-full {{ $calorie_percentage >= 100 ? 'bg-red-500' : 'bg-primary-500' }}"
                        style="--progress: {{ $calorie_percentage }}%; width: {{ $calorie_percentage }}%"></div>
                </div>
                <p class="text-xs text-slate-400 mt-1">
                    {{ $calorie_percentage >= 100 ? '🚨 Target tercapai!' : 'Sisa: ' . number_format($remaining_calories, 0) . ' kkal' }}
                </p>
            </div>

            {{-- Protein --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl stat-gradient-blue flex items-center justify-center">
                        <i class="fa-solid fa-flask text-white text-lg"></i>
                    </div>
                    <span class="text-2xl font-bold text-blue-600">{{ $protein_percentage }}%</span>
                </div>
                <p class="text-2xl font-bold text-slate-800">{{ number_format($total_protein, 1) }}g</p>
                <p class="text-slate-500 text-sm">dari {{ number_format($protein_target, 0) }}g</p>
                <div class="mt-3 h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="progress-bar h-full rounded-full {{ $protein_percentage >= 100 ? 'bg-green-500' : 'bg-blue-500' }}"
                        style="width: {{ $protein_percentage }}%"></div>
                </div>
                <p class="text-xs text-slate-400 mt-1">
                    {{ $protein_percentage >= 100 ? '✅ Target protein tercapai!' : 'Sisa: ' . number_format($remaining_protein, 1) . 'g' }}
                </p>
            </div>

            {{-- Total Entries --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl stat-gradient-orange flex items-center justify-center">
                        <i class="fa-solid fa-clipboard-list text-white text-lg"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-800">{{ $all_logs->count() }}</p>
                <p class="text-slate-500 text-sm">Catatan hari ini</p>
                <p class="text-xs text-slate-400 mt-2">
                    {{ $all_logs->where('ai_detected', true)->count() }} via AI scan
                </p>
            </div>

            {{-- Weight --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl stat-gradient-purple flex items-center justify-center">
                        <i class="fa-solid fa-weight-scale text-white text-lg"></i>
                    </div>
                    <a href="{{ route('weight-log.index') }}" class="text-xs text-primary-600 hover:underline">+ Catat</a>
                </div>
                @if($latest_weight)
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($latest_weight->weight, 1) }}<span
                            class="text-base font-medium text-slate-400">kg</span></p>
                    <p class="text-slate-500 text-sm">Berat terakhir</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $latest_weight->date->diffForHumans() }}</p>
                @else
                    <p class="text-lg font-bold text-slate-400">Belum dicatat</p>
                    <p class="text-slate-500 text-sm">Berat badan</p>
                    <a href="{{ route('weight-log.index') }}" class="text-xs text-primary-600 mt-2 block">Catat sekarang →</a>
                @endif
            </div>
        </div>

        {{-- Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Meal Summary --}}
            <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-slate-100">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800"><i class="fa-solid fa-utensils mr-1"></i> Ringkasan Makan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                    </p>
                </div>
                <div class="p-5 space-y-4">
                    @php
                        $mealIcons = ['Sarapan' => 'fa-solid fa-sun', 'Makan Siang' => 'fa-solid fa-cloud-sun', 'Makan Malam' => 'fa-solid fa-moon', 'Camilan' => 'fa-solid fa-apple-whole'];
                        $mealColors = ['Sarapan' => 'bg-amber-50 text-amber-700', 'Makan Siang' => 'bg-blue-50 text-blue-700', 'Makan Malam' => 'bg-indigo-50 text-indigo-700', 'Camilan' => 'bg-pink-50 text-pink-700'];
                    @endphp
                    @foreach($logs_by_meal as $mealType => $mealData)
                        <div
                            class="rounded-xl {{ $mealData['total_calories'] > 0 ? str_replace('text-', 'border-l-4 border-', $mealColors[$mealType]) . ' pl-3 py-2 pr-2' : '' }}">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-slate-700 text-sm">
                                    <i class="{{ $mealIcons[$mealType] }} text-sm"></i> {{ $mealType }}
                                </span>
                                @if($mealData['total_calories'] > 0)
                                    <span class="text-sm font-bold text-slate-600">
                                        {{ number_format($mealData['total_calories'], 0) }} kkal
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">Belum ada</span>
                                @endif
                            </div>
                            @if($mealData['logs']->count() > 0)
                                <div class="mt-1 space-y-0.5">
                                    @foreach($mealData['logs'] as $log)
                                        <p class="text-xs text-slate-500 flex items-center gap-1">
                                            @if($log->ai_detected)
                                                <span class="w-1.5 h-1.5 rounded-full bg-primary-400 flex-shrink-0"></span>
                                            @else
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300 flex-shrink-0"></span>
                                            @endif
                                            {{ $log->food_name }} ({{ $log->portion }}x)
                                        </p>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <a href="{{ route('food-log.index', ['date' => $date]) }}"
                        class="flex items-center justify-center gap-2 text-sm text-primary-600 font-medium hover:text-primary-700 mt-2">
                        Lihat semua →
                    </a>
                </div>
            </div>

            {{-- Calorie Chart --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800"><i class="fa-solid fa-chart-line mr-1"></i> Grafik 7 Hari
                            Terakhir</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Kalori & protein harian</p>
                    </div>
                    <div class="flex gap-4 text-xs text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-primary-500 inline-block"></span> Kalori
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> Protein
                        </span>
                    </div>
                </div>
                <div class="p-5">
                    <canvas id="dailyChart" height="200"></canvas>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const chartData = @json($chart_data);

        const ctx = document.getElementById('dailyChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Kalori (kkal)',
                        data: chartData.calories,
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: '#16a34a',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        borderSkipped: false,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Protein (g)',
                        data: chartData.protein,
                        type: 'line',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderColor: '#3b82f6',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#3b82f6',
                        pointRadius: 4,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f1923',
                        titleColor: '#fff',
                        bodyColor: '#cbd5e1',
                        padding: 12,
                        cornerRadius: 10,
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        position: 'left',
                        grid: { color: '#f1f5f9' },
                        ticks: { color: '#64748b', font: { size: 11 } },
                        title: { display: true, text: 'Kalori (kkal)', color: '#64748b', font: { size: 11 } }
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { color: '#64748b', font: { size: 11 } },
                        title: { display: true, text: 'Protein (g)', color: '#64748b', font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 11 } }
                    }
                }
            }
        });
    </script>
@endpush