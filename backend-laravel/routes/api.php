<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UUsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [UUsersController::class, 'login']);
Route::post('/register', [UUsersController::class, 'store']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User management
    Route::get('/users', [UUsersController::class, 'index']);
    Route::get('/users/{id}', [UUsersController::class, 'show']);
    Route::put('/users/{id}', [UUsersController::class, 'update']);
    Route::delete('/users/{id}', [UUsersController::class, 'destroy']);
    
    // Auth
    Route::post('/logout', [UUsersController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});