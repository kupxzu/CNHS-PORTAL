<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UUsersController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\TrackController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\TbdrsMergeController;
use App\Http\Controllers\Api\StdTbdrsMergeController;

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
    
    // Buildings
    Route::apiResource('buildings', BuildingController::class);
    
    // Rooms
    Route::apiResource('rooms', RoomController::class);
    
    // Tracks
    Route::get('/tracks', [TrackController::class, 'index']);
    Route::post('/tracks', [TrackController::class, 'store']);
    Route::get('/tracks/{id}', [TrackController::class, 'show']);
    Route::post('/tracks/initialize', [TrackController::class, 'initializeTracks']);
    
    // Departments
    Route::apiResource('departments', DepartmentController::class);
    
    // Sections
    Route::apiResource('sections', SectionController::class);
    
    // TBDRS Merge
    Route::get('/tbdrs-merge', [TbdrsMergeController::class, 'index']);
    Route::post('/tbdrs-merge', [TbdrsMergeController::class, 'store']);
    Route::get('/tbdrs-merge/{id}', [TbdrsMergeController::class, 'show']);
    Route::put('/tbdrs-merge/{id}', [TbdrsMergeController::class, 'update']);
    Route::delete('/tbdrs-merge/{id}', [TbdrsMergeController::class, 'destroy']);
    
    // Student TBDRS Merge
    Route::get('/std-tbdrs-merge', [StdTbdrsMergeController::class, 'index']);
    Route::post('/std-tbdrs-merge', [StdTbdrsMergeController::class, 'store']);
    Route::get('/std-tbdrs-merge/{id}', [StdTbdrsMergeController::class, 'show']);
    Route::get('/std-tbdrs-merge/student/{userId}', [StdTbdrsMergeController::class, 'getByStudent']);
    Route::delete('/std-tbdrs-merge/{id}', [StdTbdrsMergeController::class, 'destroy']);
});