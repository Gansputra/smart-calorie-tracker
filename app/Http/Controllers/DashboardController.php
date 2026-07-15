<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Display the user dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $date = $request->get('date', today()->format('Y-m-d'));

        $data = $this->dashboardService->getDashboardData($user, $date);

        return view('dashboard.index', $data);
    }
}
