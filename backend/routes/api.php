<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;

// Health check endpoint for Docker
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

// Public authentication routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Broadcasting authentication
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateMe']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/refresh', [AuthController::class, 'refresh']);

        Route::middleware('role:admin')->group(function () {
            Route::post('/create-user', [AuthController::class, 'createUser']);
            Route::get('/users', [AuthController::class, 'getAllUsers']);
            Route::get('/users/{id}', [AuthController::class, 'getUserById']);
            Route::put('/users/{id}', [AuthController::class, 'updateUser']);
        });
    });

    Route::prefix('travel-orders')->group(function () {
        Route::get('/', [\App\Http\Controllers\TravelOrderController::class, 'showAll']);
        Route::post('/', [\App\Http\Controllers\TravelOrderController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\TravelOrderController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\TravelOrderController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\TravelOrderController::class, 'destroy']);
        Route::get('/user/{user_id}', [\App\Http\Controllers\TravelOrderController::class, 'showAllByUser']);

        Route::middleware('role:admin')->group(function () {
            Route::put('/{id}/change-status', [\App\Http\Controllers\TravelOrderController::class, 'updateStatus']);
            Route::put('/{id}/cancel', [\App\Http\Controllers\TravelOrderController::class, 'cancel']);
        });
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unread']);
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });
});
