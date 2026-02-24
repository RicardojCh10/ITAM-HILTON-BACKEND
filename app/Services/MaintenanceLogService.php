<?php

namespace App\Services;

use App\Models\MaintenanceLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;


class MaintenanceLogService
{
    /**
     * Obtener logs. Opcional: filtrar por activo.
     */
    public function getLogs(
        $assetId = null, $perPage = 15, $event_type = null
    ): LengthAwarePaginator

    {
    $query = MaintenanceLog::with(['reporter', 'asset.member.position.department', 'asset.property']);

    if ($assetId) {
        $query->where('asset_id', $assetId);
    }
    
    // Filtros adicionales útiles para finanzas
    if($event_type) {
        $query->where('event_type', $event_type);
    }
    // if (request('event_type')) $query->where('event_type', request('event_type'));

    return $query->orderBy('event_date', 'desc')->paginate($perPage);   
    }

    public function createLog(array $data)
    {
        return MaintenanceLog::create($data);
    }

    public function getLogById($id)
    {
       return MaintenanceLog::with(['reporter', 'asset.member', 'asset.property'])->findOrFail($id);
    }

    public function updateLog($id, array $data)
    {
        $log = MaintenanceLog::findOrFail($id);
        $log->update($data);
        return $log;
    }

    public function deleteLog($id)
    {
        $log = MaintenanceLog::findOrFail($id);
        $log->delete();
    }
}