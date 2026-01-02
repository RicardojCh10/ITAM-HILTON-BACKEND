<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    // Inyectamos el servicio en el constructor
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Listar usuarios
     */
   public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $propertyId = $request->query('property_id'); // <--- Capturas esto

        // Se lo pasas al servicio
        $users = $this->userService->getAllUsers($perPage, $propertyId);
        
        return UserResource::collection($users);
    }

    /**
     * Crear usuario
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());

        return new UserResource($user);
    }

    /**
     * Mostrar un usuario específico
     */
    public function show($id)
    {
        $user = $this->userService->getUserById($id);

        return new UserResource($user);
    }
    

    /**
     * Actualizar usuario
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->userService->updateUser($id, $request->validated());

        return new UserResource($user);
    }

    /**
     * Eliminar usuario
     */
    public function destroy($id)
    {

        $this->userService->deleteUser($id);
        return response()->json(['message' => 'Usuario eliminado exitosamente']);
    }
}
