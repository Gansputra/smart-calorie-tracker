@extends('layouts.app')

@section('title', 'Jurnal Makanan')
@section('page-title', '📋 Jurnal Makanan')
@section('page-subtitle', 'Catatan asupan makan harian Anda')

@section('content')
<div class="space-y-5">

    {{-- Date & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('food-log.index', ['date' => \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d')]) }}"
               class="p-2 rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg">
                <input type="date" id="dateFilter" value="{{ $date }}"
                       class="text-sm font-medium text-slate-700 outline-none bg-transparent">
            </div>
            @if($date !== today()->format('Y-m-d'))
            <a href="{{ route('food-log.index', ['date' => \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d')]) }}"
               class="p-2 rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            @endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('scanner.index') }}" class="btn-primary btn-sm text-sm">📸 Scan AI</a>
            <a href="{{ route('food-log.create') }}" class="btn-secondary btn-sm text-sm">+ Tambah Manual</a>
        </div>
    </div>

    {{-- Summary Bar --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">Total Kalori</p>
            <p class="text-xl font-bold text-primary-600">{{ number_format($total_calories, 0) }}</p>
            <div class="mt-2 h-1.5 bg-slate-100 rounded-full">
                <div class="h-full rounded-full {{ $calorie_percentage >= 100 ? 'bg-red-500' : 'bg-primary-500' }}" style="width: {{ min(100, $calorie_percentage) }}%"></div>
            </div>
            <p class="text-[10px] text-slate-400 mt-1">{{ $calorie_percentage }}% dari {{ number_format($calorie_target) }} kkal</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">Total Protein</p>
            <p class="text-xl font-bold text-blue-600">{{ number_format($total_protein, 1) }}g</p>
            <div class="mt-2 h-1.5 bg-slate-100 rounded-full">
                <div class="h-full rounded-full {{ $protein_percentage >= 100 ? 'bg-green-500' : 'bg-blue-500' }}" style="width: {{ min(100, $protein_percentage) }}%"></div>
            </div>
            <p class="text-[10px] text-slate-400 mt-1">{{ $protein_percentage }}% dari {{ number_format($protein_target) }}g</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">Sisa Kalori</p>
            <p class="text-xl font-bold {{ $remaining_calories <= 0 ? 'text-red-500' : 'text-slate-700' }}">
                {{ $remaining_calories <= 0 ? 'Penuh!' : number_format($remaining_calories, 0) }}
            </p>
            <p class="text-[10px] text-slate-400 mt-3">kkal tersisa hari ini</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">Jumlah Entri</p>
            <p class="text-xl font-bold text-slate-700">{{ $all_logs->count() }}</p>
            <p class="text-[10px] text-slate-400 mt-3">catatan makanan</p>
        </div>
    </div>

    {{-- Logs by Meal Type --}}
    @php $mealEmojis = ['Sarapan' => '🌅', 'Makan Siang' => '☀️', 'Makan Malam' => '🌙', 'Camilan' => '🍎']; @endphp

    @foreach($logs_by_meal as $mealType => $mealData)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg">{{ $mealEmojis[$mealType] }}</span>
                    <h3 class="font-bold text-slate-800">{{ $mealType }}</h3>
                    @if($mealData['logs']->count() > 0)
                        <span class="badge-green text-xs ml-1">{{ $mealData['logs']->count() }} makanan</span>
                    @endif
                </div>
                <div class="text-right">
                    @if($mealData['total_calories'] > 0)
                        <p class="text-sm font-bold text-slate-700">{{ number_format($mealData['total_calories'], 0) }} kkal</p>
                        <p class="text-xs text-slate-400">{{ number_format($mealData['total_protein'], 1) }}g protein</p>
                    @endif
                </div>
            </div>

            @if($mealData['logs']->count() > 0)
                <div class="table-container rounded-t-none rounded-b-2xl border-t-0">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Makanan</th>
                                <th>Porsi</th>
                                <th>Kalori</th>
                                <th>Protein</th>
                                <th>Sumber</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mealData['logs'] as $log)
                                <tr>
                                    <td>
                                        <p class="font-medium text-slate-800">{{ $log->food_name }}</p>
                                        @if($log->notes)
                                            <p class="text-xs text-slate-400 mt-0.5">{{ $log->notes }}</p>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-slate-100 text-slate-600">{{ $log->portion }}x</span></td>
                                    <td class="font-semibold text-primary-600">{{ number_format($log->calories, 0) }}</td>
                                    <td class="font-semibold text-blue-600">{{ number_format($log->protein, 1) }}g</td>
                                    <td>
                                        @if($log->ai_detected)
                                            <span class="badge-green text-[10px]">🤖 AI</span>
                                        @else
                                            <span class="badge bg-slate-100 text-slate-500 text-[10px]">Manual</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('food-log.edit', $log) }}"
                                               class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('food-log.destroy', $log) }}"
                                                  onsubmit="return confirm('Hapus catatan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-6 text-center">
                    <p class="text-slate-400 text-sm">Belum ada catatan untuk {{ $mealType }}</p>
                    <a href="{{ route('food-log.create') }}" class="text-primary-600 text-xs mt-1 hover:underline">+ Tambah makanan</a>
                </div>
            @endif
        </div>
    @endforeach
</div>

<script>
document.getElementById('dateFilter').addEventListener('change', function() {
    window.location.href = '{{ route("food-log.index") }}?date=' + this.value;
});
</script>
@endsection
