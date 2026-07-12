<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inquiry\StoreInquiryRequest;
use App\Http\Requests\Inquiry\UpdateInquiryRequest;
use App\Http\Resources\InquiryResource;
use App\Services\InquiryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InquiryController extends Controller
{
    public function __construct(private InquiryService $inquiryService) {}

    public function index(Request $request)
    {
        $inquiries = $this->inquiryService->getAll(
            $request->only(['search', 'status'])
        );

        return ApiResponse::success(
            InquiryResource::collection($inquiries),
            'Inquiries retrieved successfully'
        );
    }

    public function show(int $id)
    {
        $inquiry = $this->inquiryService->getById($id);
        return ApiResponse::success(
            new InquiryResource($inquiry),
            'Inquiry retrieved successfully'
        );
    }

    public function store(StoreInquiryRequest $request)
    {
        $inquiry = $this->inquiryService->store($request->validated());
        return ApiResponse::success(
            new InquiryResource($inquiry),
            'Inquiry submitted successfully',
            201
        );
    }

    public function update(UpdateInquiryRequest $request, int $id)
    {
        $inquiry = $this->inquiryService->update($id, $request->validated());
        return ApiResponse::success(
            new InquiryResource($inquiry),
            'Inquiry updated successfully'
        );
    }

    public function destroy(int $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can delete inquiries.');
        }

        $this->inquiryService->delete($id);
        return ApiResponse::success(
            null,
            'Inquiry deleted successfully'
        );
    }
}
