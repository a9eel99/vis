<?php

namespace App\Http\Controllers\Api;

use App\Domain\Models\CarColor;
use App\Domain\Models\CarMake;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CarDataController extends Controller
{
    public function makes(): JsonResponse
    {
        $makes = Cache::remember('car.makes', 86400, function () {
            return CarMake::active()->get(['id', 'name_en', 'name_ar', 'models']);
        });

        return response()->json($makes);
    }

    public function models(int $makeId): JsonResponse
    {
        $models = Cache::remember("car.models.{$makeId}", 86400, function () use ($makeId) {
            $make = CarMake::find($makeId);
            return $make?->models ?? [];
        });

        return response()->json($models);
    }

    public function colors(): JsonResponse
    {
        $colors = Cache::remember('car.colors', 86400, function () {
            return CarColor::active()->get(['id', 'name_en', 'name_ar', 'hex']);
        });

        return response()->json($colors);
    }
}