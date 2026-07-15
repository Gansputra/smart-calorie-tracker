<?php

namespace App\Http\Controllers;

use App\Models\WeightLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeightLogController extends Controller
{
    /**
     * Display weight log list with chart data.
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $period = $request->get('period', '30'); // days

        $weightLogs = $user->weightLogs()
            ->orderBy('date', 'desc')
            ->get();

        // Prepare chart data (oldest first for chart)
        $chartLogs = $user->weightLogs()
            ->where('date', '>=', now()->subDays((int)$period))
            ->orderBy('date')
            ->get();

        $chartLabels  = $chartLogs->pluck('date')->map(fn($d) => $d->format('d/m'))->toArray();
        $chartWeights = $chartLogs->pluck('weight')->map(fn($w) => (float)$w)->toArray();

        $latestWeight = $weightLogs->first();
        $startWeight  = $weightLogs->last();
        $weightChange = null;

        if ($latestWeight && $startWeight && $latestWeight->id !== $startWeight->id) {
            $weightChange = round($latestWeight->weight - $startWeight->weight, 2);
        }

        return view('weight-log.index', compact(
            'weightLogs',
            'chartLabels',
            'chartWeights',
            'latestWeight',
            'weightChange',
            'period'
        ));
    }

    /**
     * Store a new weight log entry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'weight' => ['required', 'numeric', 'min:20', 'max:500'],
            'notes'  => ['nullable', 'string', 'max:500'],
            'date'   => ['required', 'date'],
        ], [
            'weight.required' => 'Berat badan wajib diisi.',
            'weight.min'      => 'Berat badan minimal 20 kg.',
            'weight.max'      => 'Berat badan maksimal 500 kg.',
            'date.required'   => 'Tanggal wajib diisi.',
        ]);

        WeightLog::updateOrCreate(
            ['user_id' => Auth::id(), 'date' => $validated['date']],
            ['weight' => $validated['weight'], 'notes' => $validated['notes'] ?? null]
        );

        return redirect()->route('weight-log.index')
            ->with('success', 'Berat badan berhasil dicatat! ⚖️');
    }

    /**
     * Update a weight log entry.
     */
    public function update(Request $request, WeightLog $weightLog)
    {
        $this->authorize('update', $weightLog);

        $validated = $request->validate([
            'weight' => ['required', 'numeric', 'min:20', 'max:500'],
            'notes'  => ['nullable', 'string', 'max:500'],
            'date'   => ['required', 'date'],
        ]);

        $weightLog->update($validated);

        return redirect()->route('weight-log.index')
            ->with('success', 'Data berat badan berhasil diperbarui! ✅');
    }

    /**
     * Delete a weight log entry.
     */
    public function destroy(WeightLog $weightLog)
    {
        $this->authorize('delete', $weightLog);

        $weightLog->delete();

        return redirect()->route('weight-log.index')
            ->with('success', 'Data berat badan berhasil dihapus.');
    }
}
