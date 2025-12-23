<?php

namespace App\Services;

use App\Models\MaintenanceLog;

class MaintenanceLogService
{
    /**
     * Obtener logs. Opcional: filtrar por activo.
     */
    public function getLogs($assetId = null)
    {
        $query = MaintenanceLog::query();

        if ($assetId) {
            $query->where('asset_id', $assetId);
        }

        // Ordenamos por fecha del evento (el más reciente primero)
        return $query->orderBy('event_date', 'desc')->get();
    }

    public function createLog(array $data)
    {
        return MaintenanceLog::create($data);
    }

    public function getLogById($id)
    {
        return MaintenanceLog::findOrFail($id);
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