<?php

namespace App\Http\Controllers\Dashboard;

use App\Application\Services\InspectionService;
use App\Domain\Models\Inspection;
use App\Domain\Models\Vehicle;
use App\Domain\Enums\InspectionStatus;
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
                $base = $this->inspectionService->getDashboardStats();

                // إضافة المفاتيح الناقصة التي يحتاجها الـ View
                $base['total_vehicles']      = Vehicle::count();
                $base['pending_inspections'] = Inspection::whereIn('status', [
                    InspectionStatus::DRAFT->value,
                    InspectionStatus::IN_PROGRESS->value,
                ])->count();
                $base['critical_count']      = Inspection::where('status', InspectionStatus::COMPLETED->value)
                    ->where('has_critical_failure', true)
                    ->count();
                $base['avg_score']           = $base['average_score'] ?? 0;

                return $base;
            });

            $monthlyStats = Cache::remember('dashboard_monthly', 300, function () {
                return $this->inspectionService->getMonthlyStats(12);
            });

            $recentInspections = Cache::remember('dashboard_recent', 120, function () {
                return Inspection::with(['vehicle', 'inspector'])
                    ->latest()
                    ->take(7)
                    ->get();
            });

            return view('dashboard.index', compact('stats', 'monthlyStats', 'recentInspections'));

        } catch (\Throwable $e) {
            report($e);
            return view('dashboard.index', [
                'stats'             => $this->emptyStats(),
                'monthlyStats'      => collect(),
                'recentInspections' => collect(),
            ]);
        }
    }

    private function emptyStats(): array
    {
        return [
            'total_inspections'    => 0,
            'completed_inspections'=> 0,
            'this_month'           => 0,
            'passed'               => 0,
            'failed'               => 0,
            'average_score'        => 0,
            'grade_distribution'   => [],
            'pass_rate'            => 0,
            'total_vehicles'       => 0,
            'pending_inspections'  => 0,
            'critical_count'       => 0,
            'avg_score'            => 0,
        ];
    }
}