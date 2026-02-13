<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DepartmentService;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    protected $departmentService;

    public function __construct(    DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    /**
     * Listar Departamentos
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $search = $request->query('search');

        $departments = $this->departmentService->getAllDepartments($perPage, $search);
        
        return DepartmentResource::collection($departments);
    }

    /**
     * Crear Departamento
     */
    public function store(StoreDepartmentRequest $request)
    {
        $department = $this->departmentService->createDepartment($request->validated());
        return new DepartmentResource($department);
    }

    /**
     * Ver Departamento
     */
    public function show($id)
    {
        $department = $this->departmentService->getDepartmentById($id);
        return new DepartmentResource($department);
    }

    /**
     * Actualizar Departamento
     */
    public function update(UpdateDepartmentRequest $request, $id)
    {
        $department = $this->departmentService->updateDepartment($id, $request->validated());
        return new DepartmentResource($department);
    }

    /**
     * Eliminar Departamento
     */
    public function destroy($id)
    {
        $this->departmentService->deleteDepartment($id);
        return response()->json(['message' => 'Departamento eliminado correctamente']);
    }
}
