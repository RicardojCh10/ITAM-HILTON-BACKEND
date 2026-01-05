<?php 

namespace App\Services;

use App\Models\Property;
use Illuminate\Pagination\LengthAwarePaginator;

class PropertyService
{

    public function getAllProperties($perPage = 15, $search = null): LengthAwarePaginator
    {
        $query = Property::query()->orderBy('id', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%"); 
            });
        }

        return $query->paginate($perPage);
    }

    public function CreateProperty(array $data)
    {
        return Property::create($data);
    }

    public function getPropertyById(int $id)
    {
        return Property::findOrFail($id);
    }

    public function updateProperty($id, array $data)
    {
        $property = Property::findOrFail($id);
        $property->update($data);
        return $property;
    }

    public function deleteProperty($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();
    }
}