<?php

namespace App\Http\Controllers\Api;

use App\Actions\FilterOptions\GetCarFilterOptionsAction;
use App\Actions\FilterOptions\GetDealerFilterOptionsAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class FilterOptionsController extends Controller
{
    public function cars(GetCarFilterOptionsAction $action): JsonResponse
    {
        return response()->json(['data' => $action->handle()]);
    }

    public function dealers(GetDealerFilterOptionsAction $action): JsonResponse
    {
        return response()->json(['data' => $action->handle()]);
    }
}
