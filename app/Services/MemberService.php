<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Pagination\LengthAwarePaginator;


class MemberService
{
    public function getAllMembers($perPage = 15, $propertyId = null, $search = null, $department = null, $status = null): LengthAwarePaginator
    {
        $query = Member::with('property')->orderBy('id', 'desc');

        // Filtros opcionales
        if ($propertyId) {
            $query->where('property_id', $propertyId);
        }

        if ($department) {
            $query->where('department', 'LIKE', "%{$department}%");
        }

        if ($status) {
            $query->where('status', $status); 
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('tm_id', 'LIKE', "%{$search}%")
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

    public function deleteMember($id)
    {
        $member = Member::findOrFail($id);
        $member->delete();
    }
}