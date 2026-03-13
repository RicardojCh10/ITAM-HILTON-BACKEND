<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetBatchController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\PlatformPermissionController;
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
    Route::get('/dashboard/master-metrics', [DashboardController::class, 'getMasterMetrics']);

    // Users
    Route::apiResource('users', UserController::class);

    // Departments
    Route::apiResource('departments', DepartmentController::class);

    // Positions
    Route::apiResource('positions', PositionController::class);


    // Providers
    Route::apiResource('providers', ProviderController::class);

    // Asset Categories
    Route::apiResource('asset-categories', AssetCategoryController::class);

    // Asset Batches
    Route::post('assets/import', [AssetBatchController::class, 'import']);
    Route::apiResource('assets', AssetBatchController::class);

    // Properties
    Route::apiResource('properties', PropertyController::class);

    // Members
    Route::post('members/import', [MemberController::class, 'import']);
    Route::put('/members/{id}/admit', [MemberController::class, 'admit']);
    Route::put('members/{id}/permissions', [MemberController::class, 'syncPermissions']);
    // Route::put('/members/{id}/sync-permissions', [MemberController::class, 'syncPermissions']);
    Route::get('members/{id}/access-pdf', [MemberController::class, 'downloadAccessPdf']);
    Route::get('members/stats', [MemberController::class, 'stats']);
    Route::get('members/{id}/download-assignment', [AssetBatchController::class, 'downloadAssignment']);

    Route::apiResource('members', MemberController::class);

    // Platforms
    Route::apiResource('platforms', PlatformController::class);

    // Platform Permissions
    Route::get('platforms/{platformId}/permissions', [PlatformPermissionController::class, 'getByPlatform']);
    Route::apiResource('platform-permissions', PlatformPermissionController::class);

    // Maintenance Logs
    Route::apiResource('maintenance-logs', MaintenanceLogController::class);
});
