<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Services\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(private ClientService $clientService) {}

    public function index(Request $request)
    {
        if (!in_array($request->user()->role, ['admin', 'agent'])) {
            return ApiResponse::error('Unauthorized', null, 403);
        }
        
        $clients = $this->clientService->getAll();
        return ApiResponse::success(
            ClientResource::collection($clients),
            'Clients retrieved successfully'
        );
    }

    public function show(Request $request, int $id)
    {
        if (!in_array($request->user()->role, ['admin', 'agent'])) {
            return ApiResponse::error('Unauthorized', null, 403);
        }
        
        $client = $this->clientService->getById($id);
        return ApiResponse::success(
            new ClientResource($client),
            'Client retrieved successfully'
        );
    }

    public function update(UpdateClientRequest $request, int $id)
    {
        $client = $this->clientService->updateClient($id, $request->validated());
        return ApiResponse::success(
            new ClientResource($client),
            'Client updated successfully'
        );
    }

    public function destroy(Request $request, int $id)
    {
        if ($request->user()->role !== 'admin') {
            return ApiResponse::error('Unauthorized', null, 403);
        }
        
        $this->clientService->deleteClient($id);
        return ApiResponse::success(
            null,
            'Client deleted successfully'
        );
    }
}
