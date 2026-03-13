<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Member;
use App\Services\AssetBatchService;
use App\Services\AssignmentPdfService;
use App\Http\Requests\StoreAssetBatchRequest;
use App\Http\Requests\UpdateAssetBatchRequest;
use App\Http\Resources\AssetBatchResource;
use App\Actions\CreateAssetBatchAction; // IMPORTAR LA ACCIÓN
use Illuminate\Http\Request;

class AssetBatchController extends Controller
{
    protected $assetService;
    protected $assignmentPdfService;

    public function __construct(AssetBatchService $assetService, AssignmentPdfService $assignmentPdfService)
    {
        $this->assetService = $assetService;
        $this->assignmentPdfService = $assignmentPdfService;
    }


        /**
        * Listar Activos con filtro por propiedad
        */
    public function index(Request $request)
    {
        $assets = $this->assetService->getAllAssets(
            $request->query('per_page', 15),
            $request->query('property_id'),
            $request->query('search'),
            $request->query('category_id'), // Cambiado
            $request->query('status'),
            $request->query('member_id'),
            $request->query('provider_id'),
            $request->query('department_id') // NUEVO: Pasamos el departamento
        );

        return AssetBatchResource::collection($assets);
    }

    /**
     * Crear Activos en Lote
     */
    public function store(StoreAssetBatchRequest $request, CreateAssetBatchAction $action)
    {
        // Usamos el Patrón Action para manejar la transacción compleja
        $batch = $action->execute($request->validated());

        return response()->json([
            'message' => "Se han creado {$batch->quantity} activos exitosamente.",
            'batch_id' => $batch->id
        ], 201);
    }
    /**
     * Ver Activo
     */
    public function show($id)
    {
        $asset = $this->assetService->getAssetById($id);
        if (!$asset) return response()->json(['error' => 'Activo no encontrado'], 404);
        return new AssetBatchResource($asset);
    }
    /**
     * Actualizar Activo
     */
    public function update(UpdateAssetBatchRequest $request, $id)
    {
        $asset = $this->assetService->updateAsset($id, $request->validated());
        if (!$asset) return response()->json(['error' => 'No se pudo actualizar'], 404);
        return new AssetBatchResource($asset);
    }
    /**
     * Eliminar Activo
     */
    public function destroy($id)
    {
        $this->assetService->deleteAsset($id);
        return response()->json(['message' => 'Activo eliminado exitosamente']);
    }

    /**
     * Descargar Acta de Asignación por MIEMBRO (NUEVA LÓGICA)
     */
    public function downloadAssignment($memberId) // Recibe memberId, no assetId
    {
        $member = Member::with(['assets.category', 'assets.accessories'])->findOrFail($memberId);

        if ($member->assets->count() === 0) {
            return response()->json(['message' => 'Este miembro no tiene activos asignados.'], 400);
        }

        $pdfContent = $this->assignmentPdfService->generatePdf($member);

        $filename = 'Acta_Asignacion_' . preg_replace('/[^A-Za-z0-9\-]/', '', $member->tm_id) . '.pdf';

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
      //Nuevo Endpoint para Importar
    /**
     * Importar Activos desde Excel
     */
    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);

        $this->assetService->importAssets($request->file('file'));

        return response()->json(['message' => 'Carga de activos completada']);
    }
}
