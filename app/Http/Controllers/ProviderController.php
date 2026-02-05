<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProviderService;
use App\Http\Requests\StoreProviderRequest;
use App\Http\Requests\UpdateProviderRequest;
use App\Http\Resources\ProviderResource;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    protected $providerService;

    public function __construct(ProviderService $providerService)
    {
        $this->providerService = $providerService;
    }

    /**
     * Listar Proveedores
     */
    public function index(Request $request)
    {
        // Si piden ?all=true, retornamos lista simple para selects
        if ($request->has('all')) {
            return response()->json($this->providerService->getProvidersDropdown());
        }

        $perPage = $request->query('per_page', 15);
        $search = $request->query('search');

        $providers = $this->providerService->getAllProviders($perPage, $search);
        
        return ProviderResource::collection($providers);
    }

    /**
     * Crear Proveedor
     */
    public function store(StoreProviderRequest $request)
    {
        $provider = $this->providerService->createProvider($request->validated());
        return new ProviderResource($provider);
    }

    /**
     * Ver Proveedor
     */
    public function show($id)
    {
        $provider = $this->providerService->getProviderById($id);
        return new ProviderResource($provider);
    }

    /**
     * Actualizar Proveedor
     */
    public function update(UpdateProviderRequest $request, $id)
    {
        $provider = $this->providerService->updateProvider($id, $request->validated());
        return new ProviderResource($provider);
    }

    /**
     * Eliminar Proveedor
     */
    public function destroy($id)
    {
        $this->providerService->deleteProvider($id);

        return response()->json(['message' => 'Proveedor eliminado exitosamente']);
    }
}
