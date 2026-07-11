<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Property\StorePropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Services\PropertyService;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function __construct(private PropertyService $propertyService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'type', 'status']);
        $properties = $this->propertyService->getAll($request->user(), $filters);
        return ApiResponse::success(
            PropertyResource::collection($properties),
            'Properties retrieved successfully'
        );
    }

    public function show(int $id)
    {
        $property = $this->propertyService->getById($id);
        return ApiResponse::success(
            new PropertyResource($property),
            'Property retrieved successfully'
        );
    }

    public function store(StorePropertyRequest $request)
    {
        $property = $this->propertyService->storeProperty(
            $request->validated(),
            $request->user()
        );
        return ApiResponse::success(
            new PropertyResource($property),
            'Property created successfully',
            201
        );
    }

    public function update(UpdatePropertyRequest $request, int $id)
    {
        $user = $request->user();
        $property = $this->propertyService->getById($id);

        $canUpdate = ($user->role === 'admin') ||
            ($user->role === 'agent' && $property->agent_id === $user->id);

        if (!$canUpdate) {
            return ApiResponse::error(
                'You do not have permission to edit this property',
                null,
                403
            );
        }

        $updatedProperty = $this->propertyService->updateProperty($id, $request->validated());
        return ApiResponse::success(
            new PropertyResource($updatedProperty),
            'Property updated successfully'
        );
    }

    public function destroy(Request $request, int $id)
    {
        $user = $request->user();
        $property = $this->propertyService->getById($id);

        $canDelete = ($user->role === 'admin') ||
            ($user->role === 'agent' && $property->agent_id === $user->id);

        if (!$canDelete) {
            return ApiResponse::error(
                'You do not have permission to delete this property',
                null,
                403
            );
        }

        $this->propertyService->deleteProperty($id);
        return ApiResponse::success(
            null,
            'Property deleted successfully'
        );
    }
}
