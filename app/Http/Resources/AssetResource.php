<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            // TRANSFORMACIÓN DE RELACIONES
            // Mostramos datos útiles, no solo IDs sueltos
            'location' => [
                'property_id' => $this->property_id,
                'property_name' => $this->property->name ?? 'Desconocido', // Carga el nombre del hotel
            ],

            'assigned_to' => $this->member ? [
                'member_id' => $this->member->id,
                'name' => $this->member->name,
                'email' => $this->member->email,
                'department' => $this->member->department,
            ] : null, // Si es null, significa que está en stock
            
            // DATOS DEL EQUIPO
            'info' => [
                'category' => $this->category,
                'brand' => $this->brand,
                'model' => $this->model,
                'serial_number' => $this->serial_number,
                'hilton_name' => $this->hilton_name,
            ],

            'network' => [
                'mac_address' => $this->mac_address,
                'ip_address' => $this->ip_address,
            ],

            'status' => $this->status,
            
            'dates' => [
                'purchase' => $this->purchase_date ? $this->purchase_date->format('Y-m-d') : null,
                'warranty' => $this->warranty_expiry ? $this->warranty_expiry->format('Y-m-d') : null,
            ],

            'specs' => $this->specs, // Tu JSONB dinámico

            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}