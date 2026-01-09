<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $specs = $this->specs ?? [];

        return [
            'id' => $this->id,
            
            // RELACIONES
            'location' => [
                'property_id' => $this->property_id,
                'property_name' => $this->property->name ?? 'Desconocido',
            ],

            'assigned_to' => $this->member ? [
                'member_id' => $this->member->id,
                'name' => $this->member->name,
                'email' => $this->member->email,
                'department' => $this->member->department ?? null,
            ] : null,
            
            // INFO BÁSICA
            'info' => [
                'category' => $this->category,
                'brand' => $this->brand,
                'model' => $this->model,
                'serial_number' => $this->serial_number,
                'hilton_name' => $this->hilton_name,
            ],

            // RED
            'network' => [
                'mac_address' => $this->mac_address,
                'ip_address' => $this->ip_address,
            ],

            'status' => $this->status,
            
            // FECHAS 
            'dates' => [
                'purchase' => $this->purchase_date?->format('Y-m-d'),
                'warranty' => $this->warranty_expiry?->format('Y-m-d'),
            ],

            'specs' => [
                'ram' => data_get($specs, 'ram'),
                'storage' => data_get($specs, 'storage'),
                'processor' => data_get($specs, 'processor'),
                'provider' => data_get($specs, 'provider'),
                
                'imei' => data_get($specs, 'imei'),
                'sim' => data_get($specs, 'sim'),
                'plan' => data_get($specs, 'plan'),
                'carrier' => data_get($specs, 'carrier'),
                'phone_number' => data_get($specs, 'phone_number'),
                'description' => data_get($specs, 'description'),
            ],

            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}