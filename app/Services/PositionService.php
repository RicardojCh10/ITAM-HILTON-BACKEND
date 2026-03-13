<?php

namespace App\Services;

use App\Models\Position;
use Illuminate\Pagination\LengthAwarePaginator;

class PositionService
{
    public function getAllPositions($perPage = 15, $search = null, $departmentId = null): LengthAwarePaginator
    {
        $query = Position::with('department', 'defaultPlatformPermissions')->orderBy('name', 'asc');

         if ($departmentId) {
             $query->where('department_id', $departmentId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('department', function($q2) use ($search) {
                      $q2->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        return $query->paginate($perPage);
    }

    public function createPosition(array $data)
    {
        return Position::create($data);
    }

    public function getPositionById(int $id)
    {
        return Position::with('department')->findOrFail($id);
    }

    public function updatePosition($id, array $data)
    {
        $position = Position::findOrFail($id);
        $position->update($data);
        return $position;
    }

    public function syncDefaultPermissions(int $positionId, array $permissionIds)
    {
        $position = Position::findOrFail($positionId);
        $position->defaultPlatformPermissions()->sync($permissionIds);
        
        return $position->load('defaultPlatformPermissions');
    }

    public function deletePosition($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();
    }
}