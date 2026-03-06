<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\PlatformService;
use App\Http\Requests\StorePlatformRequest;
use App\Http\Requests\UpdatePlatformRequest;
use App\Http\Resources\PlatformResource;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    protected $platformService;

    public function __construct(PlatformService $platformService)
    {
        $this->platformService = $platformService;
    }

    /**
        * Listar Plataformas
        */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $search = $request->query('search');

        $platforms = $this->platformService->getAllPlatforms($perPage, $search);

        return PlatformResource::collection($platforms);
    }

    /**
        * Crear una nueva Plataforma
        */
    public function store(StorePlatformRequest $request)
    {
        $platform = $this->platformService->createPlatform($request->validated());
        return new PlatformResource($platform);
    }

    /**
        * Mostrar una Plataforma específica
        */
    public function show($id)
    {
        $platform = $this->platformService->getPlatformById($id);
        return new PlatformResource($platform);
    }

    /**
        * Actualizar una Plataforma específica
        */
    public function update(UpdatePlatformRequest $request, $id)
    {
        $platform = $this->platformService->updatePlatform($id, $request->validated());
        return new PlatformResource($platform);
    }

    /**
        * Eliminar una Plataforma específica
        */
    public function destroy($id)
    {
        $this->platformService->deletePlatform($id);
        return response()->json(['message' => 'Plataforma eliminada correctamente']);
    }
}
