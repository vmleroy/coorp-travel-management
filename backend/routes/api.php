<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public authentication routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    Route::prefix('travel-orders')->group(function () {
        Route::get('/', [\App\Http\Controllers\TravelOrderController::class, 'showAll']);
        Route::post('/', [\App\Http\Controllers\TravelOrderController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\TravelOrderController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\TravelOrderController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\TravelOrderController::class, 'destroy']);
        Route::get('/user/{user_id}', [\App\Http\Controllers\TravelOrderController::class, 'showAllByUser']);
    });
});
