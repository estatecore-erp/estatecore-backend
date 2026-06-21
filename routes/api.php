<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PropertyController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('/register', 'register');
            Route::post('/login', 'login');

            Route::middleware('auth:sanctum')->group(function () {
                Route::get('/me', 'me');
                Route::post('/logout', 'logout');
                Route::post('/register-agent', 'registerAgent');
            });
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        // properties
        Route::prefix('properties')->controller(PropertyController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
    });
});
