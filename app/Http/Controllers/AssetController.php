<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AssetService;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
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
    /**
     * Listar Activos
     */
    public function index(Request $request)
    {
        // Filtro obligatorio por propiedad (Multitenancy)
        $propertyId = $request->query('property_id');

        $perPage = $request->query('per_page', 15); // Valor por defecto 15
        
        if (!$propertyId) {
            return response()->json(['error' => 'Property ID es requerido'], 400);
        }

        $assets = $this->assetService->getAssetsByProperty($propertyId, $perPage);
        
        // Retornamos la colección formateada
        return AssetResource::collection($assets);
    }

    // Crear un nuevo Activo
      /**
     * Crear Activo
     */
    public function store(StoreAssetRequest $request)
    {
        // El Request ya validó los datos antes de entrar aquí.
        $asset = $this->assetService->createAsset($request->validated());
        return new AssetResource($asset);
    }

    //Ver detalle de un activo
      /**
        * Ver Activo
     */
    public function show($id)
    {
        $asset = $this->assetService->getAssetById($id);

        if (!$asset) {
            return response()->json(['error' => 'Activo no encontrado'], 404);
        }

        return new AssetResource($asset);
    }

    //Actualizar un activo existente
        /**
         * Actualizar Activo
         */
    public function update(UpdateAssetRequest $request, $id)
    {
        $asset = $this->assetService->updateAsset($id, $request->validated());

        if (!$asset) {
            return response()->json(['error' => 'Activo no encontrado o no se pudo actualizar'], 404);
        }

        return new AssetResource($asset);
    }

    //Eliminar un activo
        /**
         * Eliminar Activo
         */
    public function destroy($id)
    {
        $this->assetService->deleteAsset($id);
        return response()->json(['message' => 'Activo eliminado exitosamente']);
    }
}