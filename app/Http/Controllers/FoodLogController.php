<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodLog;
use App\Services\FoodLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FoodLogController extends Controller
{
    public function __construct(
        protected FoodLogService $foodLogService
    ) {}

    /**
     * Display the food log list for a specific date.
     */
    public function index(Request $request)
    {
        $user  = Auth::user();
        $date  = $request->get('date', today()->format('Y-m-d'));

        $summary   = $this->foodLogService->getDailySummary($user, $date);
        $mealTypes = FoodLog::mealTypes();

        return view('food-log.index', array_merge($summary, compact('date', 'mealTypes')));
    }

    /**
     * Show the form for manually adding a food log.
     */
    public function create()
    {
        $mealTypes = FoodLog::mealTypes();
        $foods     = Food::active()->orderBy('name')->get();

        return view('food-log.create', compact('mealTypes', 'foods'));
    }

    /**
     * Store a manually entered food log.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'food_id'   => ['nullable', 'exists:foods,id'],
            'food_name' => ['required', 'string', 'max:255'],
            'calories'  => ['required', 'numeric', 'min:0', 'max:99999'],
            'protein'   => ['required', 'numeric', 'min:0', 'max:9999'],
            'portion'   => ['required', 'numeric', 'min:0.1', 'max:20'],
            'meal_type' => ['required', 'in:' . implode(',', FoodLog::mealTypes())],
            'notes'     => ['nullable', 'string', 'max:500'],
            'date'      => ['required', 'date'],
        ], [
            'food_name.required' => 'Nama makanan wajib diisi.',
            'calories.required'  => 'Kalori wajib diisi.',
            'protein.required'   => 'Protein wajib diisi.',
            'portion.required'   => 'Porsi wajib diisi.',
            'meal_type.required' => 'Jenis makan wajib dipilih.',
            'date.required'      => 'Tanggal wajib diisi.',
        ]);

        $nutrition = $this->foodLogService->calculateNutrition(
            $validated['calories'],
            $validated['protein'],
            (float) $validated['portion']
        );

        FoodLog::create([
            'user_id'    => Auth::id(),
            'food_id'    => $validated['food_id'] ?? null,
            'food_name'  => $validated['food_name'],
            'calories'   => $nutrition['calories'],
            'protein'    => $nutrition['protein'],
            'portion'    => $validated['portion'],
            'meal_type'  => $validated['meal_type'],
            'notes'      => $validated['notes'] ?? null,
            'date'       => $validated['date'],
            'ai_detected' => false,
        ]);

        return redirect()->route('food-log.index', ['date' => $validated['date']])
            ->with('success', 'Makanan berhasil dicatat! ✅');
    }

    /**
     * Show the form to edit a food log entry.
     */
    public function edit(FoodLog $foodLog)
    {
        $this->authorize('update', $foodLog);

        $mealTypes = FoodLog::mealTypes();
        $foods     = Food::active()->orderBy('name')->get();

        return view('food-log.edit', compact('foodLog', 'mealTypes', 'foods'));
    }

    /**
     * Update a food log entry.
     */
    public function update(Request $request, FoodLog $foodLog)
    {
        $this->authorize('update', $foodLog);

        $validated = $request->validate([
            'food_name' => ['required', 'string', 'max:255'],
            'calories'  => ['required', 'numeric', 'min:0'],
            'protein'   => ['required', 'numeric', 'min:0'],
            'portion'   => ['required', 'numeric', 'min:0.1', 'max:20'],
            'meal_type' => ['required', 'in:' . implode(',', FoodLog::mealTypes())],
            'notes'     => ['nullable', 'string', 'max:500'],
            'date'      => ['required', 'date'],
        ]);

        $nutrition = $this->foodLogService->calculateNutrition(
            $validated['calories'],
            $validated['protein'],
            (float) $validated['portion']
        );

        $foodLog->update([
            'food_name' => $validated['food_name'],
            'calories'  => $nutrition['calories'],
            'protein'   => $nutrition['protein'],
            'portion'   => $validated['portion'],
            'meal_type' => $validated['meal_type'],
            'notes'     => $validated['notes'] ?? null,
            'date'      => $validated['date'],
        ]);

        return redirect()->route('food-log.index', ['date' => $validated['date']])
            ->with('success', 'Catatan makanan berhasil diperbarui! ✅');
    }

    /**
     * Delete a food log entry.
     */
    public function destroy(FoodLog $foodLog)
    {
        $this->authorize('delete', $foodLog);

        $date = $foodLog->date->format('Y-m-d');
        $foodLog->delete();

        return redirect()->route('food-log.index', ['date' => $date])
            ->with('success', 'Catatan makanan berhasil dihapus.');
    }

    /**
     * Get food data for autocomplete (used in manual entry form).
     */
    public function getFoodData(Request $request)
    {
        $food = Food::find($request->get('food_id'));

        if (!$food) {
            return response()->json(['error' => 'Makanan tidak ditemukan'], 404);
        }

        return response()->json([
            'name'     => $food->name,
            'calories' => $food->calories_per_100g,
            'protein'  => $food->protein_per_100g,
        ]);
    }
}
