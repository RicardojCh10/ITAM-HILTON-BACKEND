<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PositionResource extends JsonResource
{
  
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'department_id' => $this->department_id,
            'name' => $this->name,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,

            'default_permissions' => $this->whenLoaded('defaultPlatformPermissions', function () {
                return $this->defaultPlatformPermissions->map(function ($perm) {
                    return [
                        'id' => $perm->id,
                        'name' => $perm->name,
                        'platform_id' => $perm->platform_id,
                        'platform_name' => $perm->platform->name ?? null,
                    ];
                });
            }),
        ];
    }
}
