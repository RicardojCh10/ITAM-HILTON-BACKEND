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
            
            'location' => [
                'property_id' => $this->property_id,
                'property_name' => $this->property?->name ?? 'Desconocido', 
            ],

            'assigned_to' => $this->member ? [
                'member_id' => $this->member->id,
                'name' => $this->member->name,
                'email' => $this->member->email,
                'department' => $this->member->department ?? null,
            ] : null,
            
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
                'purchase' => $this->purchase_date?->format('Y-m-d'),
                'warranty' => $this->warranty_expiry?->format('Y-m-d'),
            ],

            'specs' => [
                'ram' => $specs['ram'] ?? null,
                'storage' => $specs['storage'] ?? null,
                'processor' => $specs['processor'] ?? null,
                'provider' => $specs['provider'] ?? null,

                'imei' => $specs['imei'] ?? null,
                'sim' => $specs['sim'] ?? null,
                'plan' => $specs['plan'] ?? null,
                'carrier' => $specs['carrier'] ?? null,
                'phone_number' => $specs['phone_number'] ?? null,
                'description' => $specs['description'] ?? null,
            ],

            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}