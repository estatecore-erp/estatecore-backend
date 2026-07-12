<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lease\StoreLeaseRequest;
use App\Http\Requests\Lease\UpdateLeaseRequest;
use App\Http\Resources\LeaseResource;
use App\Services\LeaseService;
use Illuminate\Http\Request;

class LeaseController extends Controller
{
    public function __construct(private LeaseService $leaseService) {}

    public function index(Request $request)
    {
        $leases = $this->leaseService->getAll(
            $request->only(['search', 'status'])
        );
        return ApiResponse::success(
            LeaseResource::collection($leases),
            'Leases retrieved successfully'
        );
    }

    public function show(int $id)
    {
        $lease = $this->leaseService->getById($id);
        return ApiResponse::success(
            new LeaseResource($lease),
            'Lease retrieved successfully'
        );
    }

    public function store(StoreLeaseRequest $request)
    {
        $lease = $this->leaseService->store($request->validated());
        return ApiResponse::success(
            new LeaseResource($lease),
            'Lease created successfully',
            201
        );
    }

    public function update(UpdateLeaseRequest $request, int $id)
    {
        $lease = $this->leaseService->update($id, $request->validated());
        return ApiResponse::success(
            new LeaseResource($lease),
            'Lease updated successfully'
        );
    }

    public function destroy(int $id)
    {
        $this->leaseService->delete($id);
        return ApiResponse::success(null, 'Lease deleted successfully');
    }
}
