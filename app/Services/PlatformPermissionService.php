<?php

namespace App\Services;

use App\Models\PlatformPermission;

class PlatformPermissionService
{
    public function getByPlatform($platformId)
    {
        return PlatformPermission::where('platform_id', $platformId)->orderBy('name')->get();
    }

    public function createPlatformPermission(array $data)
    {
        return PlatformPermission::create($data);
    }

    public function getPlatformPermissionById(int $id)
    {
        return PlatformPermission::findOrFail($id);
    }

    public function updatePlatformPermission($id, array $data)
    {
        $permission = PlatformPermission::findOrFail($id);
        $permission->update($data);
        return $permission;
    }

    public function deletePlatformPermission($id)
    {
        $permission = PlatformPermission::findOrFail($id);
        
        if ($permission->positions()->count() > 0) {
            abort(400, 'No se puede eliminar el permiso porque está asociado a puestos.');
        }

        if ($permission->members()->count() > 0) {
            abort(400, 'No se puede eliminar el permiso porque está asociado a miembros.');
        }

        $permission->delete();
    }
}