<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\InquiryController;
use App\Http\Controllers\Api\V1\PropertyController;
use App\Http\Controllers\Api\V1\LeaseController;
use App\Http\Controllers\Api\V1\SaleController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

// V1 Routes
Route::prefix('/v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('/register', 'register');
            Route::post('/login', 'login');

            Route::middleware('auth:sanctum')->group(function () {
                Route::get('/me', 'me');
                Route::post('/logout', 'logout');
                Route::post('/register-agent', 'registerAgent')->middleware('role:admin');
            });
        });
    });

    Route::middleware('auth:sanctum')->group(function () {

        // users
        Route::middleware('role:admin')->prefix('users')->controller(UserController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
        });

        // properties
        Route::prefix('properties')->controller(PropertyController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store')->middleware('role:admin,agent');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        // employees
        Route::prefix('employees')->controller(EmployeeController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');

            Route::middleware('role:admin')->group(function () {
                Route::put('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
            });
        });

        // clients
        Route::prefix('clients')->controller(ClientController::class)->group(function () {
            Route::get('/', 'index')->middleware('role:admin,agent');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy')->middleware('role:admin');
        });

        // inquiries
        Route::prefix('inquiries')->controller(InquiryController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/', 'store')->middleware('role:client');
            Route::put('/{id}', 'update')->middleware('role:admin,agent');
            Route::delete('/{id}', 'destroy')->middleware('role:admin');
        });

        // leases
        Route::prefix('leases')->controller(LeaseController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/', 'store')->middleware('role:admin,agent');
            Route::put('/{id}', 'update')->middleware('role:admin');
            Route::delete('/{id}', 'destroy')->middleware('role:admin');
        });

        // sales
        Route::prefix('sales')->controller(SaleController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/', 'store')->middleware('role:admin,agent');
            Route::delete('/{id}', 'destroy')->middleware('role:admin');
        });
    });
});
