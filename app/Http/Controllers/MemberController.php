<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\MemberService;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
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
        $propertyId = $request->query('property_id');
        $perPage = $request->query('per_page', 15);
        if (!$propertyId) {
            return response()->json(['error' => 'Property ID es requerido'], 400);
        }

        $members = $this->memberService->getMembersByProperty($propertyId, $perPage);
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
     * Eliminar Miembro
     */
    public function destroy($id)
    {
        $this->memberService->deleteMember($id);

        return response()->json(['message' => 'Miembro eliminado exitosamente']);
    }
}
