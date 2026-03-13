<?php

namespace App\Services;

use App\Models\Asset;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class AssetBatchService
{
    public function getAllAssets(
        $perPage = 15, 
        $propertyId = null, 
        $search = null, 
        $categoryId = null, 
        $status = null,
        $memberId = null,
        $providerId = null,
        $departmentId = null 
    ): LengthAwarePaginator
    {
        $query = Asset::with(['property', 'member.position.department', 'provider', 'category', 'accessories'])->orderBy('id', 'desc');

        if ($propertyId) $query->where('property_id', $propertyId);
        if ($categoryId) $query->where('category_id', $categoryId); 
        if ($status) $query->where('status', $status);
        if ($memberId) $query->where('member_id', $memberId);
        if ($providerId) $query->where('provider_id', $providerId);

        if ($departmentId) {
            $query->whereHas('member.position', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('brand', 'LIKE', "%{$search}%")
                  ->orWhere('model', 'LIKE', "%{$search}%")
                  ->orWhere('serial_number', 'LIKE', "%{$search}%")
                  ->orWhere('hilton_name', 'LIKE', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function getAssetById($id)
    {
        return Asset::with(['property', 'member.position.department', 'provider', 'maintenanceLogs', 'category', 'accessories'])->findOrFail($id);
    }

    public function updateAsset($id, array $data)
    {
        $asset = Asset::findOrFail($id);
        
        if (isset($data['specs'])) {
            $currentSpecs = $asset->specs ?? [];
            $data['specs'] = array_merge($currentSpecs, $data['specs']);
        }

        $asset->update($data);

        if (array_key_exists('accessories_base', $data)) {
            $asset->accessories()->delete(); 
            
            if (!empty($data['accessories_base'])) {
                foreach ($data['accessories_base'] as $acc) {
                    $asset->accessories()->create([
                        'type' => $acc['type'],
                        'brand' => $acc['brand'] ?? null,
                        'serial_number' => $acc['serial_number'] ?? null,
                    ]);
                }
            }
        }
        return $asset->refresh()->load(['property', 'member.position.department', 'category', 'accessories']);
    }

    public function deleteAsset($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();
    }

    public function importAssets($file)
    {
        DB::transaction(function () use ($file) {
            Excel::import(new \App\Imports\AssetsImport, $file);
        });
    }
}