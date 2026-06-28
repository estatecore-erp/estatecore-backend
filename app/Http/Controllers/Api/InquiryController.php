<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInquiryRequest;
use App\Services\InquiryService;
use App\Http\Resources\InquiryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    protected $inquiryService;

    public function __construct(InquiryService $inquiryService)
    {
        $this->inquiryService = $inquiryService;
    }

    public function index(): JsonResponse
    {
        return ApiResponse::success(
            InquiryResource::collection($this->inquiryService->getFilteredInquiries()),
            'Inquiries retrieved successfully'
        );
    }

    public function store(StoreInquiryRequest $request): JsonResponse
    {
        try {
            $inquiry = $this->inquiryService->store($request->validated());
            return ApiResponse::success(new InquiryResource($inquiry), 'Inquiry created successfully', 201);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to create inquiry', $e->getMessage(), 500);
        }
    }

    public function show($id): JsonResponse
    {
        $inquiry = $this->inquiryService->findById($id);
        return ApiResponse::success(new InquiryResource($inquiry), 'Inquiry retrieved successfully');
    }

    public function update(Request $request, $id): JsonResponse
    {
       
        $request->validate(['status' => 'required|in:pending,responded']);
        $inquiry = $this->inquiryService->updateStatus($id, $request->only('status'));
        return ApiResponse::success(new InquiryResource($inquiry), 'Status updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $this->inquiryService->delete($id);
        return ApiResponse::success(null, 'Inquiry deleted successfully');
    }
}