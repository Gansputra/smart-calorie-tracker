<?php

namespace App\Http\Controllers;

use App\Models\FoodLog;
use App\Services\AiServerService;
use App\Services\FoodLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FoodScannerController extends Controller
{
    public function __construct(
        protected AiServerService $aiServerService,
        protected FoodLogService $foodLogService
    ) {}

    /**
     * Show the food scanner page.
     */
    public function index()
    {
        $aiStatus = $this->aiServerService->healthCheck();
        $mealTypes = FoodLog::mealTypes();

        return view('scanner.index', compact('aiStatus', 'mealTypes'));
    }

    /**
     * Process uploaded image through AI server and return prediction.
     */
    public function predict(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
        ], [
            'image.required' => 'Gambar wajib diunggah.',
            'image.image'    => 'File harus berupa gambar.',
            'image.mimes'    => 'Format gambar harus JPEG, JPG, PNG, atau WebP.',
            'image.max'      => 'Ukuran gambar maksimal 10MB.',
        ]);

        $prediction = $this->aiServerService->predictFood($request->file('image'));

        if (!$prediction['success']) {
            return response()->json([
                'success' => false,
                'message' => $prediction['error'] ?? 'Prediksi gagal.',
            ], 422);
        }

        return response()->json([
            'success'    => true,
            'food_name'  => $prediction['food_name'],
            'calories'   => $prediction['calories'],
            'protein'    => $prediction['protein'],
            'confidence' => $prediction['confidence'],
        ]);
    }

    /**
     * Save AI prediction result to food log.
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'food_name'  => ['required', 'string', 'max:255'],
            'calories'   => ['required', 'numeric', 'min:0'],
            'protein'    => ['required', 'numeric', 'min:0'],
            'confidence' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'portion'    => ['required', 'numeric', 'min:0.1', 'max:20'],
            'meal_type'  => ['required', 'in:' . implode(',', FoodLog::mealTypes())],
            'notes'      => ['nullable', 'string', 'max:500'],
        ]);

        $this->foodLogService->createFromAiPrediction(
            Auth::user(),
            [
                'food_name'  => $validated['food_name'],
                'calories'   => $validated['calories'],
                'protein'    => $validated['protein'],
                'confidence' => $validated['confidence'] ?? null,
            ],
            (float) $validated['portion'],
            $validated['meal_type'],
            $validated['notes'] ?? ''
        );

        return redirect()->route('food-log.index')
            ->with('success', 'Makanan berhasil disimpan ke jurnal! 🍽️');
    }
}
