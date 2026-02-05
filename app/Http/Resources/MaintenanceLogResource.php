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
            'event_type' => $this->event_type,
            
            'current_holder' => ($this->asset && $this->asset->member) ? [
                'id' => $this->asset->member->id,
                'full_name' => $this->asset->member->full_name, 
                'department' => $this->asset->member->department,
                'position' => $this->asset->member->position,
            ] : null,

            // DATOS DEL ACTIVO
            'asset' => [
                'id' => $this->asset?->id,
                'hilton_name' => $this->asset?->hilton_name ?? 'Desconocido',
                'serial_number' => $this->asset?->serial_number ?? '-',
                'category' => $this->asset?->category ?? '-',
                
                'location' => [
                    'property_name' => $this->asset?->property?->name ?? 'Sin Ubicación'
                ],
            ],

            'reporter' => $this->reporter ? [
                'id' => $this->reporter->id,
                'full_name' => trim($this->reporter->name . ' ' . ($this->reporter->last_name ?? '')),
                'email' => $this->reporter->email,
            ] : null,

            'details' => [
                'title' => $this->title,
                'description' => $this->description,
                'cost' => (float) $this->cost,
            ],
            
            'dates' => [
                'event_date' => $this->event_date ? $this->event_date->format('Y-m-d') : null,
                'resolved_date' => $this->resolved_date ? $this->resolved_date->format('Y-m-d') : null,
                'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            ],
        ];
    }
}