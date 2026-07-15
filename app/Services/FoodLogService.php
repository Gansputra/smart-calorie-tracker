<?php

namespace App\Services;

use App\Models\FoodLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * FoodLogService
 *
 * Handles business logic for food logging, including calorie/protein
 * calculations based on portion size and date-based queries.
 */
class FoodLogService
{
    /**
     * Calculate macronutrients for a given portion.
     * Base is always per 100g (1 portion = 100g).
     *
     * @param  float  $caloriesPer100g
     * @param  float  $proteinPer100g
     * @param  float  $portion  e.g. 1.5 = 150g
     * @return array{calories: float, protein: float}
     */
    public function calculateNutrition(
        float $caloriesPer100g,
        float $proteinPer100g,
        float $portion
    ): array {
        return [
            'calories' => round($caloriesPer100g * $portion, 2),
            'protein'  => round($proteinPer100g * $portion, 2),
        ];
    }

    /**
     * Get daily summary for a user on a specific date.
     *
     * @param  User    $user
     * @param  string  $date  YYYY-MM-DD format
     * @return array{
     *     total_calories: float,
     *     total_protein: float,
     *     calorie_percentage: float,
     *     protein_percentage: float,
     *     logs_by_meal: array
     * }
     */
    public function getDailySummary(User $user, string $date): array
    {
        $logs = $user->foodLogs()
            ->whereDate('date', $date)
            ->orderBy('created_at')
            ->get();

        $totalCalories = $logs->sum('calories');
        $totalProtein  = $logs->sum('protein');

        $calorieTarget = $user->calorie_target ?: 2000;
        $proteinTarget = $user->protein_target ?: 150;

        $logsByMeal = [];
        foreach (FoodLog::mealTypes() as $mealType) {
            $mealLogs = $logs->where('meal_type', $mealType);
            $logsByMeal[$mealType] = [
                'logs'            => $mealLogs,
                'total_calories'  => $mealLogs->sum('calories'),
                'total_protein'   => $mealLogs->sum('protein'),
            ];
        }

        return [
            'total_calories'       => round($totalCalories, 1),
            'total_protein'        => round($totalProtein, 1),
            'calorie_target'       => $calorieTarget,
            'protein_target'       => $proteinTarget,
            'calorie_percentage'   => $calorieTarget > 0 ? min(100, round(($totalCalories / $calorieTarget) * 100, 1)) : 0,
            'protein_percentage'   => $proteinTarget > 0 ? min(100, round(($totalProtein / $proteinTarget) * 100, 1)) : 0,
            'remaining_calories'   => max(0, round($calorieTarget - $totalCalories, 1)),
            'remaining_protein'    => max(0, round($proteinTarget - $totalProtein, 1)),
            'logs_by_meal'         => $logsByMeal,
            'all_logs'             => $logs,
        ];
    }

    /**
     * Get calorie intake data for the last N days for charting.
     *
     * @param  User  $user
     * @param  int   $days
     * @return array{labels: array, calories: array, protein: array}
     */
    public function getChartData(User $user, int $days = 7): array
    {
        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();

        $data = $user->foodLogs()
            ->select(
                DB::raw('DATE(date) as log_date'),
                DB::raw('SUM(calories) as total_calories'),
                DB::raw('SUM(protein) as total_protein')
            )
            ->where('date', '>=', $startDate)
            ->groupBy('log_date')
            ->orderBy('log_date')
            ->get()
            ->keyBy('log_date');

        $labels   = [];
        $calories = [];
        $protein  = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date      = Carbon::now()->subDays($i)->format('Y-m-d');
            $label     = Carbon::now()->subDays($i)->format('d/m');
            $dayData   = $data->get($date);

            $labels[]   = $label;
            $calories[] = $dayData ? round($dayData->total_calories, 1) : 0;
            $protein[]  = $dayData ? round($dayData->total_protein, 1) : 0;
        }

        return compact('labels', 'calories', 'protein');
    }

    /**
     * Create a food log entry from AI prediction result.
     *
     * @param  User   $user
     * @param  array  $prediction  AI server response
     * @param  float  $portion     Serving size
     * @param  string $mealType
     * @param  string $notes
     * @return FoodLog
     */
    public function createFromAiPrediction(
        User $user,
        array $prediction,
        float $portion,
        string $mealType,
        string $notes = ''
    ): FoodLog {
        $nutrition = $this->calculateNutrition(
            $prediction['calories'],
            $prediction['protein'],
            $portion
        );

        return FoodLog::create([
            'user_id'        => $user->id,
            'food_name'      => $prediction['food_name'],
            'calories'       => $nutrition['calories'],
            'protein'        => $nutrition['protein'],
            'portion'        => $portion,
            'meal_type'      => $mealType,
            'notes'          => $notes,
            'date'           => today()->format('Y-m-d'),
            'ai_detected'    => true,
            'ai_confidence'  => $prediction['confidence'] ?? null,
        ]);
    }
}
