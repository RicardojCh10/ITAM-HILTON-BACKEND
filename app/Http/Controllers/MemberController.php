<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\MemberService;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Actions\ProvisionMemberAccessAction;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    protected $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * Listar Miembros por hotel
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $propertyId = $request->query('property_id'); 
        $search = $request->query('search');    
        $department = $request->query('department');
        $status = $request->query('status');
        $positionId = $request->query('position_id');

        $members = $this->memberService->getAllMembers($perPage, $propertyId, $search, $department, $status, $positionId);
        
        return MemberResource::collection($members);
    }

    /**
     * Crear Miembro
     */
    public function store(StoreMemberRequest $request)
    {
        $member = $this->memberService->createMember($request->validated());
        return new MemberResource($member);
    }

    /**
     * Ver Miembro
     */
    public function show($id)
    {
        $member = $this->memberService->getMemberById($id);
        return new MemberResource($member);
            // $member = Member::with(['property', 'position.department'])->findOrFail($id);
            // return new MemberResource($member);
    }

    /**
     * Actualizar Miembro
     */
    public function update(UpdateMemberRequest $request, $id)
    {
        $member = $this->memberService->updateMember($id, $request->validated());
        return new MemberResource($member);
    }

   /**
     * Admitir Miembro en ITAM
     */
    public function admit($id)
    {
        $member = $this->memberService->admitMember($id);

        return response()->json([
            'message' => 'Admission Date registrada. Miembro ACTIVO operativamente.',
            'data' => new MemberResource($member)
        ]);
    }

     /**
     * Sincronizar Permisos de Plataforma (ITAM) - Acción específica para cuando se actualizan los permisos manualmente desde el Front o se asignan nuevos permisos a la posición.
     */
    public function syncPermissions(Request $request, $id, ProvisionMemberAccessAction $action)
    {
        // 1. Validar request al vuelo (es un array de IDs)
        $request->validate([
            'permissions'   => 'present|array',
            'permissions.*' => 'integer|exists:platform_permissions,id'
        ]);

        $member = $this->memberService->getMemberById($id);
        
        // 2. Delegamos la lógica matemática compleja a la Acción
        $updatedMember = $action->execute($member, $request->permissions);

        return response()->json([
            'message' => 'Accesos a plataformas actualizados y auditados.',
            'data' => new MemberResource($updatedMember)
        ]);
    }

    /**
     * Dar de baja en ITAM
     */
    public function destroy($id)
    {
        $member = $this->memberService->retireMember($id);

        return response()->json([
            'message' => 'Baja de IT registrada. El estado se ha recalculado.',
            'status' => $member->status // Te devuelve BAJA o TERMINADO según la matemática
        ]);
    }

    /**
     * Importar Miembros desde Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $this->memberService->importMembers($request->file('file'));
        return response()->json(['message' => 'Importación completada']);
    }

    /** 
     * Obtener estadísticas quincenales de altas y bajas
     */
    public function stats()
    {
        return response()->json($this->memberService->getSimpleStats());
    }

}
