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

            'position_details' => $this->position ? [ 
                'id' => $this->position->id,
                'name' => $this->position->name,
                'department_id' => $this->position->department_id,
                'department' => $this->position->department ? [
                    'id' => $this->position->department->id,
                    'name' => $this->position->department->name,
                ] : null,
                
                // NUEVO: Mandamos el Blueprint (lo que IT debería asignarle)
                'default_permissions' => $this->position->relationLoaded('defaultPlatformPermissions') 
                    ? $this->position->defaultPlatformPermissions->map(function ($perm) {
                        return [
                            'id' => $perm->id,
                            'name' => $perm->name,
                            'platform_id' => $perm->platform_id,
                        ];
                    }) 
                    : [],
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
         
            'hire_date' => $this->hire_date ? Carbon::parse($this->hire_date)->format('Y-m-d') : null,
            'termination_date' => $this->termination_date ? Carbon::parse($this->termination_date)->format('Y-m-d') : null,
            'admission_date' => $this->admission_date ? Carbon::parse($this->admission_date)->format('Y-m-d') : null,
            'hire_end_date' => $this->hire_end_date ? Carbon::parse($this->hire_end_date)->format('Y-m-d') : null,
            'status' => $this->status,
            'details' => $this->details, // Devuelve el JSON completo

            'platform_permissions' => $this->whenLoaded('platformPermissions', function () {
                return $this->platformPermissions->map(function ($perm) {
                    return [
                        'id' => $perm->id,
                        'name' => $perm->name,
                        'platform_id' => $perm->platform_id,
                        'platform_name' => $perm->platform->name ?? null,
                        
                        // Metadatos de Auditoría (Tabla Pivote)
                        'is_override' => (bool) $perm->pivot->is_override,
                        'granted_by'  => $perm->pivot->granted_by,
                    ];
                });
            }),

            'assets' => $this->whenLoaded('assets', function () {
                return $this->assets->map(function ($asset) {
                    return [
                        'id' => $asset->id,
                        'brand' => $asset->brand,
                        'model' => $asset->model,
                        'category_name' => $asset->category->name ?? 'Hardware'
                    ];
                });
            }),
            
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
