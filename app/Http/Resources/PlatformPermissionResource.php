<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlatformPermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'platform_id' => $this->platform_id,
            'name' => $this->name,
            'description' => $this->description,
            // Opcional: Cargar nombre de plataforma si viene en la relación
            'platform_name' => $this->whenLoaded('platform', fn() => $this->platform->name),
        ];
    }
}