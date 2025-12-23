<?php

namespace App\Services;

use App\Models\Member;

class MemberService
{
    public function getMembersByProperty($propertyId)
    {
        return Member::where('property_id', $propertyId)->get();
    }

    public function createMember(array $data)
    {
        return Member::create($data);
    }

    public function getMemberById(int $id)
    {
        return Member::findOrFail($id);
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