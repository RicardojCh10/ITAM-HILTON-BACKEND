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

    public function store(StoreAssetRequest $request)
    {
        // El Request ya validó los datos antes de entrar aquí.
        // Llamamos al servicio con todos los datos (el servicio sabrá qué hacer)
        $newAsset = $this->assetService->createAsset($request->all());

        return new AssetResource($newAsset);
    }
}