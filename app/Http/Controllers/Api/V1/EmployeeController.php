<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(private EmployeeService $employeeService) {}

    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return ApiResponse::error('Unauthorized', null, 403);
        }
        
        $employees = $this->employeeService->getAll();
        return ApiResponse::success(
            EmployeeResource::collection($employees),
            'Employees retrieved successfully'
        );
    }

    public function show(Request $request, int $id)
    {
        if ($request->user()->role !== 'admin') {
            return ApiResponse::error('Unauthorized', null, 403);
        }
        
        $employee = $this->employeeService->getById($id);
        return ApiResponse::success(
            new EmployeeResource($employee),
            'Employee retrieved successfully'
        );
    }

    public function update(UpdateEmployeeRequest $request, int $id)
    {
        $employee = $this->employeeService->updateEmployee($id, $request->validated());
        return ApiResponse::success(
            new EmployeeResource($employee),
            'Employee updated successfully'
        );
    }

    public function destroy(Request $request, int $id)
    {
        if ($request->user()->role !== 'admin') {
            return ApiResponse::error('Unauthorized', null, 403);
        }
        
        $this->employeeService->deleteEmployee($id);
        return ApiResponse::success(
            null,
            'Employee deleted successfully'
        );
    }
}
