<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
   //Login: Recibe emnail y password, devuelve token JWT
    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized', 'message' => 'Invalid credentials'], 401);
        }

        return $this->respondWithToken($token);
    }

/**
     * Obtener usuario actual (Me)
     */    
    public function me()
    {
        return response()->json(auth('api')->user());
    }

/**
     * Cerrar Sesión (Logout)
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

/**
     * Refrescar Token
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    //Estructura de la respuesta con el token JWT
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60, // minutos a segundos
            'user' => auth('api')->user(),
        ]);
    }
}