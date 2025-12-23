<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MaintenanceLogController;

 // Rutas Publicas de Autenticación
  Route::post('auth/login', [AuthController::class, 'login']);

  // Rutas Protegidas de Autenticación
  Route::group(['middleware' => ['auth:api']], function () {

    //Auth
      Route::post('auth/logout', [AuthController::class, 'logout']);
      Route::post('auth/refresh', [AuthController::class, 'refresh']);
      Route::get('auth/me', [AuthController::class, 'me']);

      // Rutas Protegidas de Recursos
      Route::middleware('auth:api')->group(function () {
      // Recursos API
      //Rutas de assets
      Route::apiResource('assets', AssetController::class);
      //Rutas de Property
      Route::apiResource('properties', PropertyController::class);
      //Rutas de Members
      Route::apiResource('members', MemberController::class);
      //Rutas de Maintenance Logs
      Route::apiResource('maintenance-logs', MaintenanceLogController::class);
      });

  });