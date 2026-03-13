<?php

namespace App\Services;

use App\Models\AssetCategory;
use Illuminate\Pagination\LengthAwarePaginator;

class AssetCategoryService
{
    public function getAll($perPage = 15, $search = null): LengthAwarePaginator
    {
        $query = AssetCategory::orderBy('name', 'asc');

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('prefix', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('prefix', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }
        return $query->paginate($perPage); 
    }

    public function create(array $data)
    {
        return AssetCategory::create($data);
    }

    public function update($id, array $data)
    {
        $category = AssetCategory::findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function delete($id)
    {
        $category = AssetCategory::findOrFail($id);
        
        // Validación de negocio: No borrar si tiene activos
        if ($category->assets()->count() > 0) {
            abort(400, 'No se puede eliminar la categoría porque tiene activos asignados.');
        }

        $category->delete();
    }
}