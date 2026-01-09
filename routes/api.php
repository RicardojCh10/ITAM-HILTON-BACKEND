<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MaintenanceLogController;

// Rutas Publicas
Route::post('auth/login', [AuthController::class, 'login']);

// Rutas Protegidas (Todo lo de adentro requiere Token)
Route::group(['middleware' => ['auth:api']], function () {

    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
    Route::get('auth/me', [AuthController::class, 'me']);

    // --- RECURSOS API ---

    // Users
    Route::apiResource('users', UserController::class);

    // Assets 
    Route::get('/assets/{id}/download-assignment', [AssetController::class, 'downloadAssignment']);
    Route::apiResource('assets', AssetController::class);

    // Otros recursos
    Route::apiResource('properties', PropertyController::class);
    Route::apiResource('members', MemberController::class);
    Route::apiResource('maintenance-logs', MaintenanceLogController::class);

});