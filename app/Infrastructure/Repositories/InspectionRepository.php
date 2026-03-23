<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Enums\InspectionGrade;
use App\Domain\Enums\InspectionStatus;
use App\Domain\Models\Inspection;
use App\Domain\Repositories\Contracts\InspectionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InspectionRepository extends BaseRepository implements InspectionRepositoryInterface
{
    public function __construct(Inspection $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model
            ->with(['vehicle', 'inspector', 'template'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, $columns);
    }

    public function getByVehicle(string $vehicleId): Collection
    {
        return $this->model
            ->where('vehicle_id', $vehicleId)
            ->with(['inspector', 'template'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByInspector(string $inspectorId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('inspector_id', $inspectorId)
            ->with(['vehicle', 'template'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getCompleted(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->completed()
            ->with(['vehicle', 'inspector', 'template'])
            ->orderBy('completed_at', 'desc')
            ->paginate($perPage);
    }

    public function getRecentCompleted(int $limit = 10): Collection
    {
        return $this->model
            ->completed()
            ->with(['vehicle', 'inspector'])
            ->orderBy('completed_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getMonthlyStats(int $months = 12): Collection
    {
        return $this->model
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("AVG(CASE WHEN percentage IS NOT NULL THEN percentage ELSE NULL END) as avg_score")
            )
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function getDashboardStats(): array
    {
        $total = $this->model->count();
        $completed = $this->model->completed()->count();
        $thisMonth = $this->model->thisMonth()->count();

        $gradeDistribution = $this->model
            ->completed()
            ->select('grade', DB::raw('COUNT(*) as count'))
            ->groupBy('grade')
            ->pluck('count', 'grade')
            ->toArray();

        $avgScore = $this->model
            ->completed()
            ->avg('percentage') ?? 0;

        $passed = $this->model
            ->completed()
            ->where('has_critical_failure', false)
            ->whereIn('grade', [InspectionGrade::EXCELLENT->value, InspectionGrade::GOOD->value])
            ->count();

        $failed = $completed - $passed;

        return [
            'total_inspections' => $total,
            'completed_inspections' => $completed,
            'this_month' => $thisMonth,
            'passed' => $passed,
            'failed' => $failed,
            'average_score' => round($avgScore, 1),
            'grade_distribution' => $gradeDistribution,
            'pass_rate' => $completed > 0 ? round(($passed / $completed) * 100, 1) : 0,
        ];
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->filter($query, null, null, $perPage);
    }

    public function filter(?string $search = null, ?string $status = null, ?string $grade = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->with(['vehicle', 'inspector', 'template']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function ($vq) use ($search) {
                      $vq->where('make', 'like', "%{$search}%")
                         ->orWhere('model', 'like', "%{$search}%")
                         ->orWhere('license_plate', 'like', "%{$search}%");
                  })
                  ->orWhereHas('inspector', function ($iq) use ($search) {
                      $iq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($grade) {
            $query->where('grade', $grade);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getWithFullDetails(string $id)
    {
        return $this->model
            ->with([
                'vehicle.customer',
                'template.sections.questions',
                'results.question.section',
                'results.media',
                'inspector',
                'creator',
                'media',
            ])
            ->findOrFail($id);
    }
}
