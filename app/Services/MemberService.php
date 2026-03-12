<?php

namespace App\Services;

use App\Models\Member;
use Carbon\Carbon;
use App\Imports\MembersImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Actions\ProvisionMemberAccessAction;

class MemberService
{
    public function getAllMembers(
        $perPage = 15,
        $propertyId = null,
        $search = null,
        $department = null,
        $status = null,
        $positionId = null
    ): LengthAwarePaginator {
        $query = Member::with(['property', 'position.department'])->orderBy('id', 'desc');

        // Filtros opcionales
        if ($propertyId) {
            $query->where('property_id', $propertyId);
        }

        if ($positionId) {
            $query->where('position_id', $positionId);
        }

        if ($department) {
            $query->whereHas('position.department', function ($q) use ($department) {
                $q->where('name', 'LIKE', "%{$department}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('tm_id', 'LIKE', "%{$search}%")
                    ->orWhere('hilton_id', 'LIKE', "%{$search}%")
                    ->orWhere('onq_id', 'LIKE', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function createMember(array $data)
    {
        return Member::create($data);
        $this->autoSyncPositionPermissions($member);
    }

    public function getMemberById(int $id)
    {
        return Member::with(
            'property',
            'position.department',
            'position.defaultPlatformPermissions',
            'platformPermissions.platform',
            'assets'
        )->findOrFail($id);
    }

    public function updateMember($id, array $data)
    {
        $member = Member::findOrFail($id);
        $oldPositionId = $member->position_id;

        if (isset($data['details'])) {
            $currentDetails = $member->details ?? [];
            $data['details'] = array_merge($currentDetails, $data['details']);
        }
        $member->update($data);

        // Si se cambió de posición, sincronizamos permisos
        if (isset($data['position_id']) && $data['position_id'] != $oldPositionId) {
            $this->autoSyncPositionPermissions($member);
        }
        return $member;
    }

    protected function autoSyncPositionPermissions(Member $member)
    {
        if ($member->position_id) {
            $member->load('position.defaultPlatformPermissions');
            
            $blueprintIds = [];
            if ($member->position) {
                $blueprintIds = $member->position->defaultPlatformPermissions->pluck('id')->toArray();
            }

            app(ProvisionMemberAccessAction::class)->execute($member, $blueprintIds);
        }
    }

    public function admitMember($id)
    {
        $member = Member::findOrFail($id);

        if (is_null($member->admission_date)) {
            $member->update([
                'admission_date' => Carbon::now(),
            ]);
        }

        return $member;
    }

    /**
     * PROCESO IT: BAJA DE USUARIO
     */
    public function retireMember($id)
    {
        $member = Member::findOrFail($id);

        $member->update([
            'termination_date' => Carbon::now(),
            // No pasamos 'status' => 'BAJA', el modelo lo calcula matemáticamente.
        ]);

        return $member;
    }

    /**
     *  PROCESO RH: FIN DE CONTRATO (Opcional, si el Front de RH lo invoca)
     */
    public function setRhTerminationDate($id, $date)
    {
        $member = Member::findOrFail($id);
        $member->update([
            'hire_end_date' => $date, // Fecha que RH indica que acabó el contrato
        ]);
        return $member;
    }

    /**
     * Estadísticas Simples (Dashboard)
     */
    public function getSimpleStats()
    {
        return [
            'total_members' => Member::count(),

            // Operativos (Ya tienen laptop/accesos)
            'active'        => Member::where('status', Member::STATUS_ACTIVE)->count(),

            // Pendientes de IT (RH ya los creó, falta que IT les de activos)
            'pending_it'    => Member::where('status', Member::STATUS_PENDING_IT)->count(),

            // En proceso de salida (Falta cerrar cuentas o devolver equipos)
            'offboarding'   => Member::where('status', Member::STATUS_OFFBOARDING)->count(),
        ];
    }

    // Método para importar miembros desde un archivo Excel
    public function importMembers($file)
    {
        DB::transaction(function () use ($file) {
            Excel::import(new MembersImport, $file);
        });
    }
}
