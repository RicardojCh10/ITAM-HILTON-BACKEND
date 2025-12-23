<?php

namespace App\Services;

use App\Models\Asset;

class AssetService
{
    public function getAssetsByProperty($propertyId, $perPage = 15)
    {
        return Asset::with(['property', 'member']) // <--- CLAVE: Carga los datos relacionados de una vez
                    ->where('property_id', $propertyId)
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
    }

    public function createAsset(array $data)
    {
        // Creamos el activo
        $asset = Asset::create($data);
        
        // Recargamos las relaciones para que el Resource pueda mostrar el nombre del miembro inmediatamente
        return $asset->load(['property', 'member']);
    }

    public function getAssetById($id)
    {
        return Asset::with(['property', 'member', 'maintenanceLogs'])->findOrFail($id);
    }

    public function updateAsset($id, array $data)
    {
        $asset = Asset::findOrFail($id);
        
        // Lógica para fusionar specs sin borrar las anteriores (opcional)
        if (isset($data['specs'])) {
            $currentSpecs = $asset->specs ?? [];
            $data['specs'] = array_merge($currentSpecs, $data['specs']);
        }

        $asset->update($data);
        
        // Retornamos el activo fresco con sus relaciones actualizadas
        return $asset->refresh()->load(['property', 'member']);
    }

    public function deleteAsset($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();
    }
}