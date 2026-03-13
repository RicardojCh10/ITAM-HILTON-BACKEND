<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'prefix' => $this->prefix,
            'icon' => $this->icon,
            'is_serialized' => (bool) $this->is_serialized,
            'has_network_fields' => (bool) $this->has_network_fields,
        ];
    }
}