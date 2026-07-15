<?php

namespace App\Services;

use App\Models\FoodLog;
use App\Models\User;
use App\Models\WeightLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * DashboardService
 *
 * Aggregates all data needed for the user dashboard including
 * daily summaries, streaks, and recent activity.
 */
class DashboardService
{
    public function __construct(
        protected FoodLogService $foodLogService
    ) {}

    /**
     * Get all data needed for the user dashboard.
     *
     * @param  User    $user
     * @param  string  $date  YYYY-MM-DD format
     * @return array
     */
    public function getDashboardData(User $user, string $date): array
    {
        $dailySummary = $this->foodLogService->getDailySummary($user, $date);
        $chartData    = $this->foodLogService->getChartData($user, 7);
        $latestWeight = $user->weightLogs()->latest('date')->first();
        $recentLogs   = $user->foodLogs()
            ->whereDate('date', $date)
            ->latest()
            ->take(5)
            ->get();

        return array_merge($dailySummary, [
            'chart_data'    => $chartData,
            'latest_weight' => $latestWeight,
            'recent_logs'   => $recentLogs,
            'date'          => $date,
            'user'          => $user,
        ]);
    }

    /**
     * Get admin dashboard statistics.
     *
     * @return array
     */
    public function getAdminStats(): array
    {
        $totalUsers    = User::role('user')->count();
        $totalAdmins   = User::role('admin')->count();
        $totalFoodLogs = FoodLog::count();
        $totalFoods    = \App\Models\Food::count();

        $recentUsers = User::latest()->take(5)->get();
        $recentLogs  = FoodLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        $userGrowth = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return compact(
            'totalUsers',
            'totalAdmins',
            'totalFoodLogs',
            'totalFoods',
            'recentUsers',
            'recentLogs',
            'userGrowth'
        );
    }
}
