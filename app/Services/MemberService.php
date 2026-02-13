<?php

namespace App\Services;

use App\Models\Member;
use Carbon\Carbon;
use App\Imports\MembersImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;

class MemberService
{
    public function getAllMembers($perPage = 15,
     $propertyId = null,
      $search = null,
       $department = null,
        $status = null,
         $positionId = null): LengthAwarePaginator
    {
        $query = Member::with(['property', 'position.department'])->orderBy('id', 'desc');
        
        // Filtros opcionales
        if ($propertyId) {
            $query->where('property_id', $propertyId);
        }

        if ($positionId) {
            $query->where('position_id', $positionId);
        }

        if ($department) {
        $query->whereHas('position.department', function($q) use ($department) {
            $q->where('name', 'LIKE', "%{$department}%");
        });
    }

        if ($status) {
            $query->where('status', $status); 
        }

        if ($search) {
            $query->where(function($q) use ($search) {
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
    }

    public function getMemberById(int $id)
    {
        return Member::with('property')->findOrFail($id);
    }

    public function updateMember($id, array $data)
    {
        $member = Member::findOrFail($id);

        if (isset($data['details'])){
            $currentDetails = $member->details ?? [];
            $data['details'] = array_merge($currentDetails, $data['details']);
        }
        $member->update($data);
        return $member;
    }

    // public function deleteMember($id)
    // {
    //     $member = Member::findOrFail($id);
    //     $member->delete();
    // }

    // Nuevo método para dar de baja a un miembro
    public function retireMember($id)
    {
        $member = Member::findOrFail($id);
        $member->update([
            'status' => 'BAJA',
            'termination_date' => Carbon::now(),
        ]);

        return $member;
    }

    // Método para obtener estadísticas quincenales de altas y bajas
    public function getBiweeklyStats()
    {
        $startDate = Carbon::now()->subDays(15);
        $endDate = Carbon::now();

        // Contamos Altas (basado en hire_date o created_at según tu regla de negocio)
        $altas = Member::whereBetween('hire_date', [$startDate, $endDate])->count();

        // Contamos Bajas (basado en termination_date)
        $bajas = Member::where('status', 'BAJA')
                       ->whereBetween('termination_date', [$startDate, $endDate])
                       ->count();

        return [
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString()
            ],
            'stats' => [
                'altas' => $altas,
                'bajas' => $bajas,
                'difference' => $altas - $bajas // Balance neto
            ]
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