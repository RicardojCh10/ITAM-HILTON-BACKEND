<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\PositionService;
use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use App\Http\Resources\PositionResource;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    protected $positionService;

    public function __construct(PositionService $positionService)
    {
        $this->positionService = $positionService;
    }

    /**
     * Listar Puestos por hotel
     */
    public function index(Request $request) 
    {
        $perPage = $request->query('per_page', 15);
        $search = $request->query('search');
        $departmentId = $request->query('department_id');

        $positions = $this->positionService->getAllPositions($perPage, $search, $departmentId);
        
        return PositionResource::collection($positions);
    }

    /**
     * Crear Puesto
     */
    public function store(StorePositionRequest $request)
    {
        $position = $this->positionService->createPosition($request->validated());
        
        if ($request->has('default_permissions')) {
            $this->positionService->syncDefaultPermissions($position->id, $request->default_permissions);
        }
        return new PositionResource($position);
    }

    /**
     * Ver Puesto
     */
    public function show($id)
    {
        $position = $this->positionService->getPositionById($id);
        return new PositionResource($position->load('defaultPlatformPermissions.platform'));
    }

    /**
     * Actualizar Puesto
     */
    public function update(UpdatePositionRequest $request, $id)
    {
        $position = $this->positionService->updatePosition($id, $request->validated());
        
        // NUEVO: Sincronizamos al actualizar
        if ($request->has('default_permissions')) {
            $this->positionService->syncDefaultPermissions($position->id, $request->default_permissions);
        }

        return new PositionResource($position->load('defaultPlatformPermissions.platform'));
    }

    /**
     * Eliminar Puesto
     */
    public function destroy($id)
    {
        $this->positionService->deletePosition($id);
        return response()->json(['message' => 'Puesto eliminado correctamente']);
    }
}
