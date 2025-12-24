<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\MaintenanceLogService;
use App\Http\Requests\StoreMaintenanceLogRequest;
use App\Http\Requests\UpdateMaintenanceLogRequest;
use App\Http\Resources\MaintenanceLogResource;
use Illuminate\Http\Request;

class MaintenanceLogController extends Controller
{
    protected $maintenanceLogService;

    public function __construct(MaintenanceLogService $maintenanceLogService)
    {
        $this->maintenanceLogService = $maintenanceLogService;
    }

    /**
     * Listar Registros de Mantenimiento
     */
    public function index(Request $request)
    {
        $assetId = $request->query('asset_id');
        $perPage = $request->query('per_page', 15);
        $logs = $this->maintenanceLogService->getLogs($assetId, $perPage);

        return MaintenanceLogResource::collection($logs);
    }

    /**
     * Crear Registro de Mantenimiento
     */
    public function store(StoreMaintenanceLogRequest $request)
    {
        $data = $request->validated();

        $data['reported_by'] = auth()->id();
        $log = $this->maintenanceLogService->createLog($data);
        return new MaintenanceLogResource($log);    
    }

    /**
     * Ver Registro de Mantenimiento
     */
    public function show($id)
    {
        $log = $this->maintenanceLogService->getLogById($id);
        return new MaintenanceLogResource($log);
    }

    /**
     * Actualizar Registro de Mantenimiento
     */
    public function update(UpdateMaintenanceLogRequest $request, $id)
    {
        $log = $this->maintenanceLogService->updateLog($id, $request->validated());
        return new MaintenanceLogResource($log);
    }

    /**
     * Eliminar Registro de Mantenimiento
     */
    public function destroy($id)
    {
        $this->maintenanceLogService->deleteLog($id);

        return response()->json(['message' => 'Registro de mantenimiento eliminado exitosamente']);
    }
}
