<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Services\AssetService;
use App\Services\AssignmentPdfService;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Http\Resources\AssetResource;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    protected $assetService;
    protected $assignmentPdfService;

    // Inyección de Dependencia del Servicio
        public function __construct(AssetService $assetService, AssignmentPdfService $assignmentPdfService)

    {
        $this->assetService = $assetService;
        $this->assignmentPdfService = $assignmentPdfService;
    }

    // Listar Activos con filtro por propiedad
    /**
     * Listar Activos
     */
    public function index(Request $request)
    {
        // Captura de filtros
        $perPage = $request->query('per_page', 15);
        $propertyId = $request->query('property_id');
        $search = $request->query('search');
        $category = $request->query('category');
        $status = $request->query('status');
        $memberId = $request->query('member_id');

       $assets = $this->assetService->getAllAssets(
            $perPage, 
            $propertyId, 
            $search, 
            $category, 
            $status,
            $memberId
        );
        
        // Retornamos la colección formateada
        return AssetResource::collection($assets);
    }

    public function downloadAssignment($id)
    {
        // Buscamos el activo y cargamos la relación con el empleado
        $asset = Asset::with(['assigned_to', 'location'])->findOrFail($id);

        if (!$asset->member_id) {
            return response()->json(['message' => 'Este activo no está asignado a nadie.'], 400);
        }

        // Generamos el binario del PDF
        $pdfContent = $this->assignmentPdfService->generatePdf($asset);

        // Preparamos el nombre del archivo (Sanitizado)
        $filename = 'Acta_' . preg_replace('/[^A-Za-z0-9\-]/', '', $asset->serial_number) . '.pdf';

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    // Crear un nuevo Activo
      /**
     * Crear Activo
     */
    public function store(StoreAssetRequest $request)
    {
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