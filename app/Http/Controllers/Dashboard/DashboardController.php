<?php

namespace App\Http\Controllers\Dashboard;

use App\Application\Services\InspectionService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct(
        private InspectionService $inspectionService
    ) {}

    public function index()
    {
        try {
            $stats = Cache::remember('dashboard_stats', 300, function () {
                return $this->inspectionService->getDashboardStats();
            });

            $monthlyStats = Cache::remember('dashboard_monthly', 300, function () {
                return $this->inspectionService->getMonthlyStats(12);
            });

            return view('dashboard.index', compact('stats', 'monthlyStats'));
        } catch (\Throwable $e) {
            report($e);
            return view('dashboard.index', [
                'stats' => [],
                'monthlyStats' => collect(),
            ]);
        }
    }
}