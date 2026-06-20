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
        $properties = $this->propertyService->getAll($request->user());
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
        $property = $this->propertyService->updateProperty($id, $request->validated());
        return ApiResponse::success(
            new PropertyResource($property),
            'Property updated successfully'
        );
    }

    public function destroy(int $id)
    {
        $this->propertyService->deleteProperty($id);
        return ApiResponse::success(
            null,
            'Property deleted successfully'
        );
    }
}
