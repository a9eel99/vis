<?php

namespace App\Application\Services;

use App\Domain\Models\Inspection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RevenueService
{
    /**
     * Get revenue summary for dashboard
     */
    public function getSummary(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        return [
            'today' => $this->getRevenue($today, $today->copy()->endOfDay()),
            'this_month' => $this->getRevenue($thisMonth, Carbon::now()),
            'last_month' => $this->getRevenue($lastMonth, $lastMonthEnd),
            'total_unpaid' => $this->getUnpaidTotal(),
            'today_count' => $this->getCount($today, $today->copy()->endOfDay()),
            'this_month_count' => $this->getCount($thisMonth, Carbon::now()),
        ];
    }

    /**
     * Get daily revenue report
     */
    public function getDailyReport(string $month = null): array
    {
        $date = $month ? Carbon::parse($month . '-01') : Carbon::now();
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();

        $daily = Inspection::where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereBetween('paid_at', [$start, $end])
            ->select(
                DB::raw('DATE(paid_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(price - discount) as revenue'),
                DB::raw('SUM(discount) as total_discount')
            )
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->orderBy('date')
            ->get();

        $totalRevenue = $daily->sum('revenue');
        $totalCount = $daily->sum('count');
        $totalDiscount = $daily->sum('total_discount');

        return [
            'month' => $date->format('Y-m'),
            'month_label' => $date->translatedFormat('F Y'),
            'days' => $daily,
            'total_revenue' => $totalRevenue,
            'total_count' => $totalCount,
            'total_discount' => $totalDiscount,
            'avg_per_day' => $daily->count() > 0 ? round($totalRevenue / $daily->count(), 2) : 0,
            'avg_per_inspection' => $totalCount > 0 ? round($totalRevenue / $totalCount, 2) : 0,
        ];
    }

    /**
     * Get monthly revenue report (12 months)
     */
    public function getMonthlyReport(int $months = 12): array
    {
        $start = Carbon::now()->subMonths($months - 1)->startOfMonth();

        $monthly = Inspection::where('status', 'completed')
            ->where('payment_status', 'paid')
            ->where('paid_at', '>=', $start)
            ->select(
                DB::raw("DATE_FORMAT(paid_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(price - discount) as revenue'),
                DB::raw('SUM(discount) as total_discount')
            )
            ->groupBy(DB::raw("DATE_FORMAT(paid_at, '%Y-%m')"))
            ->orderBy('month')
            ->get();

        return [
            'months' => $monthly,
            'total_revenue' => $monthly->sum('revenue'),
            'total_count' => $monthly->sum('count'),
        ];
    }

    /**
     * Get unpaid inspections
     */
    public function getUnpaidInspections()
    {
        return Inspection::where('status', 'completed')
            ->where('payment_status', 'unpaid')
            ->with(['vehicle', 'template', 'inspector'])
            ->latest()
            ->get();
    }

    /**
     * Mark inspection as paid
     */
    public function markAsPaid(string $inspectionId, float $discount = 0, ?string $note = null): Inspection
    {
        $inspection = Inspection::findOrFail($inspectionId);
        $inspection->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'discount' => $discount,
            'payment_note' => $note,
        ]);
        return $inspection;
    }

    /**
     * Mark inspection as unpaid
     */
    public function markAsUnpaid(string $inspectionId): Inspection
    {
        $inspection = Inspection::findOrFail($inspectionId);
        $inspection->update([
            'payment_status' => 'unpaid',
            'paid_at' => null,
            'discount' => 0,
        ]);
        return $inspection;
    }

    private function getRevenue($from, $to): float
    {
        return (float) Inspection::where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereBetween('paid_at', [$from, $to])
            ->sum(DB::raw('price - discount'));
    }

    private function getUnpaidTotal(): float
    {
        return (float) Inspection::where('status', 'completed')
            ->where('payment_status', 'unpaid')
            ->sum('price');
    }

    private function getCount($from, $to): int
    {
        return Inspection::where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereBetween('paid_at', [$from, $to])
            ->count();
    }
}