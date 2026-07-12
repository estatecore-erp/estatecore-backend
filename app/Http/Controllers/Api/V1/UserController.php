<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index(Request $request)
    {
        $users = $this->userService->getAll($request->query('type'));
        return ApiResponse::success(
            UserResource::collection($users),
            'Users retrieved successfully'
        );
    }

    public function show(int $id)
    {
        $user = $this->userService->getById($id);
        return ApiResponse::success(
            new UserResource($user),
            'User retrieved successfully'
        );
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        $user = $this->userService->update($id, $request->validated());
        return ApiResponse::success(
            new UserResource($user),
            'User updated successfully'
        );
    }

    public function destroy(int $id)
    {
        $this->userService->delete($id);
        return ApiResponse::success(
            null,
            'User deleted successfully'
        );
    }
}
