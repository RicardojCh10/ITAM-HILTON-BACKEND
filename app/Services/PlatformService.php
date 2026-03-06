<?php

namespace App\Services;

use App\Models\Platform;
use Illuminate\Pagination\LengthAwarePaginator;

class PlatformService
{
    public function getAllPlatforms($perPage = 15, $search = null): LengthAwarePaginator
    {
        $query = Platform::with('permissions')->orderBy('name', 'asc');

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    public function createPlatform(array $data)
    {
        return Platform::create($data);
    }

    public function getPlatformById(int $id)
    {
        return Platform::with('permissions')->findOrFail($id);
    }

    public function updatePlatform($id, array $data)
    {
        $platform = Platform::findOrFail($id);
        $platform->update($data);
        return $platform;
    }

    public function deletePlatform($id)
    {
        $platform = Platform::findOrFail($id);
        
        if ($platform->permissions()->count() > 0) {
            abort(400, 'No se puede eliminar la plataforma porque tiene permisos asociados.');
        }

        $platform->delete();
    }

}