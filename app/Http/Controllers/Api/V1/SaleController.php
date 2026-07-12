<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Http\Resources\SaleResource;
use App\Services\SaleService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(private SaleService $saleService) {}

    public function index(Request $request)
    {
        $sales = $this->saleService->getAll(
            $request->only(['search', 'status'])
        );
        return ApiResponse::success(
            SaleResource::collection($sales),
            'Sales retrieved successfully'
        );
    }

    public function show(int $id)
    {
        $sale = $this->saleService->getById($id);
        return ApiResponse::success(
            new SaleResource($sale),
            'Sale retrieved successfully'
        );
    }

    public function store(StoreSaleRequest $request)
    {
        $sale = $this->saleService->store($request->validated());
        return ApiResponse::success(
            new SaleResource($sale),
            'Sale created successfully',
            201
        );
    }

    public function destroy(int $id)
    {
        $this->saleService->delete($id);
        return ApiResponse::success(
            null,
            'Sale deleted successfully'
        );
    }
}
