<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'tm_id' => $this->tm_id,
            'name' => $this->name,
            'email' => $this->email,
            'corporate_info' => [
                'position' => $this->position,
                'department' => $this->department,
                'onq_id' => $this->onq_id,
            ],
            'status' => $this->status,
            'details' => $this->details, // Devuelve el JSON completo
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
