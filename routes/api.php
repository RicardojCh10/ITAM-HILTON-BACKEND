<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;

// Ruta de TEST para verificar que la API responde
Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
});

// Rutas de Activos (Assets)
// GET  /api/assets?property_id=1  -> Listar equipos de un hotel
// POST /api/assets                -> Crear un equipo nuevo
Route::get('/assets', [AssetController::class, 'index']);
Route::post('/assets', [AssetController::class, 'store']);

// (Opcional) Ruta para obtener el usuario logueado, útil más adelante
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});