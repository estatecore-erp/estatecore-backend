<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterAgentRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Helpers\ApiResponse;
use App\Models\Client;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    // Client self registration
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'role' => 'client',
        ]);

        Client::create([
            'user_id' => $user->id,
            'address' => $request->address,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponse::success([
            'token' => $token,
            'user'  => new UserResource($user),
        ], 'Registration successful', Response::HTTP_CREATED);
    }

    // Admin creates agent
    public function registerAgent(RegisterAgentRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'role' => 'agent',
        ]);

        Employee::create([
            'user_id' => $user->id,
            'hire_date' => today(),
        ]);

        return ApiResponse::success([
            'user' => new UserResource($user),
        ], 'Agent created successfully', Response::HTTP_CREATED);
    }

    // Login for everyone
    public function login(LoginRequest $request)
    {
        $user = User::query()->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::error(
                'Invalid credentials',
                null,
                Response::HTTP_UNAUTHORIZED
            );
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponse::success([
            'token' => $token,
            'user'  => new UserResource($user),
        ], 'Login successful');
    }

    // Get current logged in user
    public function me(Request $request)
    {
        return ApiResponse::success(
            new UserResource($request->user()),
            'User retrieved successfully'
        );
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return ApiResponse::success(null, 'Logged out successfully');
    }
}
