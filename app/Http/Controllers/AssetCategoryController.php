<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AssetCategoryService;
use App\Http\Requests\StoreAssetCategoryRequest;
use App\Http\Requests\UpdateAssetCategoryRequest;
use App\Http\Resources\AssetCategoryResource;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    protected $categoryService;

    public function __construct(AssetCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Listar Categorías de Activos
     */

    public function index(Request $request)
    {
        $categories = $this->categoryService->getAll(
            $request->query('per_page', 15),
            $request->query('search')
        );

        return AssetCategoryResource::collection($categories);     
        
    }

    /**
     * Crear Categoría de Activo
     */
    public function store(StoreAssetCategoryRequest $request)
    {
        $category = $this->categoryService->create($request->validated());
        return new AssetCategoryResource($category);
    }

    /**
     * Actualizar Categoría de Activo
     */
    public function update(UpdateAssetCategoryRequest $request, $id)
    {
        $category = $this->categoryService->update($id, $request->validated());
        return new AssetCategoryResource($category);
    }

    /**
     * Eliminar Categoría de Activo
     */
    public function destroy($id)
    {
        $this->categoryService->delete($id);
        return response()->json(['message' => 'Categoría eliminada exitosamente']);
    }
}