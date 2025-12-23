<?php 

namespace App\Services;

use App\Models\Property;

class PropertyService
{
    public function getAllProperties()
    {
        return Property::all();
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