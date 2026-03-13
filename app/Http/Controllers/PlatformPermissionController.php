<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\PlatformPermissionService;
use App\Http\Requests\StorePlatformPermissionRequest;
use App\Http\Requests\UpdatePlatformPermissionRequest;
use App\Http\Resources\PlatformPermissionResource;

class PlatformPermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PlatformPermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    // Usualmente los permisos se piden filtrados por plataforma

    /**
     * Listar Permisos de Plataforma por Plataforma
     */
    public function getByPlatform($platformId)
    {
        $permissions = $this->permissionService->getByPlatform($platformId);
        return PlatformPermissionResource::collection($permissions);
    }

    /**
     * Crear un nuevo Permiso de Plataforma
     */
    public function store(StorePlatformPermissionRequest $request)
    {
        $permission = $this->permissionService->createPlatformPermission($request->validated());
        return new PlatformPermissionResource($permission);
    }

    /**
     * Mostrar un Permiso de Plataforma específico
     */
    public function show($id)
    {
        $permission = $this->permissionService->getPlatformPermissionById($id);
        return new PlatformPermissionResource($permission);
    }

    /**
     * Actualizar un Permiso de Plataforma específico
     */
    public function update(UpdatePlatformPermissionRequest $request, $id)
    {
        // Se inyecta 'permission' como parámetro en la ruta api.php
        $permission = $this->permissionService->updatePlatformPermission($id, $request->validated());
        return new PlatformPermissionResource($permission);
    }

    /**
     * Eliminar un Permiso de Plataforma específico
     */
    public function destroy($id)
    {
        $this->permissionService->deletePlatformPermission($id);
        return response()->json(['message' => 'Permiso eliminado correctamente']);
    }
}
