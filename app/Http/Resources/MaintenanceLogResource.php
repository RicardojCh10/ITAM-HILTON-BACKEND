<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asset_id' => $this->asset_id,
            'reported_by_id' => $this->reported_by,
            'event_type' => $this->event_type,
            'details' => [
                'title' => $this->title,
                'description' => $this->description,
                'cost' => (float) $this->cost,
            ],
            'dates' => [
                'event_date' => $this->event_date,
                'resolved_date' => $this->resolved_date,
                'created_at' => $this->created_at->toIso8601String(),
            ],
       ];
    }
}
