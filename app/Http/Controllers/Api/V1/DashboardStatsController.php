<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardStatsResource;
use App\Services\DashboardStatsService;
use Illuminate\Http\Request;

class DashboardStatsController extends Controller
{
    public function __construct(private DashboardStatsService $service) {}

    public function stats(Request $request)
    {
        $data = $this->service->getStats($request->user());

        return ApiResponse::success(
            new DashboardStatsResource($data),
            'Dashboard stats fetched'
        );
    }
}
