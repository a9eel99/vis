<?php

namespace App\Http\Controllers\Api;

use App\Domain\Models\CarMake;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CarDataController extends Controller
{
    public function makes(): JsonResponse
    {
        $makes = CarMake::active()->get(['id', 'name_en', 'name_ar', 'models']);
        return response()->json($makes);
    }

    public function models(int $makeId): JsonResponse
    {
        $make = CarMake::find($makeId);
        if (!$make) return response()->json([]);
        return response()->json($make->models ?? []);
    }

    public function colors(): JsonResponse
    {
        $colors = \App\Domain\Models\CarColor::active()->get(['id', 'name_en', 'name_ar', 'hex']);
        return response()->json($colors);
    }
}