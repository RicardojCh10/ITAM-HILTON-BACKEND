<?php

namespace App\Actions;

use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProvisionMemberAccessAction
{
    /**
     * Asigna permisos a un miembro calculando excepciones respecto a su puesto base.
     * * @param Member $member El usuario al que le daremos acceso
     * @param array $selectedPermissionIds Array de IDs de platform_permissions que IT marcó en el UI
     */
    public function execute(Member $member, array $selectedPermissionIds): Member
    {
        return DB::transaction(function () use ($member, $selectedPermissionIds) {
            
            // 1. Obtener el Blueprint: ¿Qué permisos dice su puesto que debería tener?
            $defaultPermissionIds = [];
            if ($member->position) {
                $defaultPermissionIds = $member->position->defaultPlatformPermissions()
                                               ->pluck('platform_permissions.id')
                                               ->toArray();
            }

            // 2. Preparar la data para la tabla pivote (member_platform_permission)
            $syncData = [];
            $adminId = Auth::id(); // Saber qué cuenta de IT está haciendo el cambio

            foreach ($selectedPermissionIds as $permissionId) {
                // Si el permiso que le estamos dando NO está en su puesto, es una excepción
                $isOverride = !in_array($permissionId, $defaultPermissionIds);

                $syncData[$permissionId] = [
                    'is_override' => $isOverride,
                    'granted_by'  => $adminId,
                ];
            }

            // 3. Sincronizar: Borra permisos que IT desmarcó, inserta los nuevos con sus metadatos
            $member->platformPermissions()->sync($syncData);

            // 4. Si el miembro era PENDING_IT, al darle su primer acceso, lo marcamos como ADMITIDO
            if (is_null($member->admission_date) && count($selectedPermissionIds) > 0) {
                // Reutilizamos la lógica del modelo que ya programaste
                $member->update(['admission_date' => now()]);
            }

            return $member->refresh()->load('platformPermissions.platform');
        });
    }
}