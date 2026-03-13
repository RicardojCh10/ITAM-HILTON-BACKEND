<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Actions\ProvisionMemberAccessAction;
use Illuminate\Http\Request;

class MemberAccessController extends Controller
{
    public function syncPermissions(Request $request, $memberId, ProvisionMemberAccessAction $action)
    {
        $request->validate([
            'permissions' => 'present|array',
            'permissions.*' => 'integer|exists:platform_permissions,id'
        ]);

        $member = Member::findOrFail($memberId);
        
        // Ejecutamos la lógica de negocio compleja
        $updatedMember = $action->execute($member, $request->permissions);

        return response()->json([
            'message' => 'Accesos actualizados correctamente.',
            'data' => $updatedMember // O pásalo por un Resource si prefieres
        ]);
    }
}