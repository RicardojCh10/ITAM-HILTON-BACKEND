<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\PropertyService;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    protected $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    /**
     * Listar Propiedades
     */
    public function index()
    {
        $properties = $this->propertyService->getAllProperties();
        return PropertyResource::collection($properties);
    }

    /**
     * Crear Propiedad
     */
    public function store(StorePropertyRequest $request)
    {
        $property = $this->propertyService->createProperty($request->validated());
        return new PropertyResource($property);
    }

    /**
     * Ver Propiedad
     */
    public function show($id)
    {
        $property = $this->propertyService->getPropertyById($id);
        return new PropertyResource($property);
    }

    /**
     * Actualizar Propiedad
     */
    public function update(UpdatePropertyRequest $request, $id)
    {
        $property = $this->propertyService->updateProperty($id, $request->validated());
        return new PropertyResource($property);
    }

    /**
     * Eliminar Propiedad
     */
    public function destroy($id)
    {
        $this->propertyService->deleteProperty($id);

        return response()->json(['message' => 'Propiedad eliminada exitosamente']);
    }
}