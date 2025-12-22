<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AssetService;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Resources\AssetResource;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    protected $assetService;

    // Inyección de Dependencia del Servicio
    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    // Listar Activos con filtro por propiedad
    public function index(Request $request)
    {
        // Filtro obligatorio por propiedad (Multitenancy)
        $propertyId = $request->query('property_id');
        
        if (!$propertyId) {
            return response()->json(['error' => 'Property ID es requerido'], 400);
        }

        $assets = $this->assetService->getAssetsByProperty($propertyId);
        
        // Retornamos la colección formateada
        return AssetResource::collection($assets);
    }

    // Crear un nuevo Activo
    public function store(StoreAssetRequest $request)
    {
        // El Request ya validó los datos antes de entrar aquí.
        $newAsset = $this->assetService->createAsset($request->all());

        return new AssetResource($newAsset);
    }

    //Ver detalle de un activo
    public function show($id)
    {
        $asset = $this->assetService->getAssetById($id);

        if (!$asset) {
            return response()->json(['error' => 'Activo no encontrado'], 404);
        }

        return new AssetResource($asset);
    }

    //Actualizar un activo existente
    public function update(Request $request, $id)
    {
        $updatedAsset = $this->assetService->updateAsset($id, $request->all());

        if (!$updatedAsset) {
            return response()->json(['error' => 'Activo no encontrado o no se pudo actualizar'], 404);
        }

        return new AssetResource($updatedAsset);
    }

    //Eliminar un activo
    public function destroy($id)
    {
        $deleted = $this->assetService->deleteAsset($id);

        if (!$deleted) {
            return response()->json(['error' => 'Activo no encontrado o no se pudo eliminar'], 404);
        }

        return response()->json(['message' => 'Activo eliminado exitosamente']);
    }
}