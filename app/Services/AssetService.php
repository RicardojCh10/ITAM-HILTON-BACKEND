<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Pagination\LengthAwarePaginator;

class AssetService
{
    public function getAssetsByProperty(
        $perPage = 15, 
        $propertyId = null, 
        $search = null, 
        $categoryId = null, 
        $status = null,
        $memberId = null
        ): LengthAwarePaginator
    {
        $query = Asset::with(['property', 'member'])->orderBy('id', 'desc');

        // --- FILTROS ---
        if ($propertyId) $query->where('property_id', $propertyId);
        if ($categoryId) $query->where('category', $categoryId);
        if ($status) $query->where('status', $status);
        if ($memberId) $query->where('member_id', $memberId);

        // --- BÚSQUEDA AVANZADA ---
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('brand', 'LIKE', "%{$search}%")
                  ->orWhere('model', 'LIKE', "%{$search}%")
                  ->orWhere('serial_number', 'LIKE', "%{$search}%")
                  ->orWhere('hilton_name', 'LIKE', "%{$search}%")
                  ->orWhere('mac_address', 'LIKE', "%{$search}%")
                  ->orWhere('ip_address', 'LIKE', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function createAsset(array $data)
    {
        $asset = Asset::create($data);
        
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
        
        // Se recarga el activo fresco con sus relaciones actualizadas
        return $asset->refresh()->load(['property', 'member']);
    }

    public function deleteAsset($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();
    }
}