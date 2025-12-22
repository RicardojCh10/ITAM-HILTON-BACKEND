<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;

 // Rutas Publicas de Autenticación
  Route::post('auth/login', [AuthController::class, 'login']);

  // Rutas Protegidas de Autenticación
  Route::group(['middleware' => ['auth:api']], function () {

    //Auth
      Route::post('auth/logout', [AuthController::class, 'logout']);
      Route::post('auth/refresh', [AuthController::class, 'refresh']);
      Route::get('auth/me', [AuthController::class, 'me']);

    // Rutas de Activos (Assets)
      Route::middleware('auth:api')->group(function () {
        //CREA TODAS LAS RUTAS (GET, POST, PUT, DELETE)
        Route::apiResource('assets', AssetController::class);
        });
  });