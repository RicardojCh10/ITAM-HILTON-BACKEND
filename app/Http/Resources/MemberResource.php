<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'position_id' => $this->position_id,

            'property' => $this->whenLoaded('property', function () {
                return [
                    'id' => $this->property->id,
                    'name' => $this->property->name,
                    'code' => $this->property->code,
                ];
            }),

            'position_details' => $this->position ? [ // Verificamos si existe la relación (no es null)
                'id' => $this->position->id,
                'name' => $this->position->name,
                'department_id' => $this->position->department_id,
                'department' => $this->position->department ? [
                    'id' => $this->position->department->id,
                    'name' => $this->position->department->name,
                ] : null,
            ] : null,

            'tm_id' => $this->tm_id,
            'hilton_id' => $this->hilton_id,

            'name' => $this->name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,

            'corporate_info' => [
                'position' => $this->position?->name ?? 'Sin Puesto',

                'department' => $this->position?->department?->name ?? 'Sin Departamento',

                'onq_id' => $this->onq_id,
            ],

            // 'hire_date' => $this->hire_date ? $this->hire_date->format('Y-m-d') : null,
            // 'termination_date' => $this->termination_date ? $this->termination_date->format('Y-m-d') : null,
            // 'admission_date' => $this->admission_date ? $this->admission_date->format('Y-m-d') : null,
            // 'hire_end_date' => $this->hire_end_date ? $this->hire_end_date->format('Y-m-d') : null,
            'hire_date' => $this->hire_date ? Carbon::parse($this->hire_date)->format('Y-m-d') : null,
            'termination_date' => $this->termination_date ? Carbon::parse($this->termination_date)->format('Y-m-d') : null,
            'admission_date' => $this->admission_date ? Carbon::parse($this->admission_date)->format('Y-m-d') : null,
            'hire_end_date' => $this->hire_end_date ? Carbon::parse($this->hire_end_date)->format('Y-m-d') : null,
            'status' => $this->status,
            'details' => $this->details, // Devuelve el JSON completo
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
