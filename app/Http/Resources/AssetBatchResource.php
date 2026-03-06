<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetBatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $specs = $this->specs ?? [];

        return [
            'id' => $this->id,
            
            'location' => [
                'property_id' => $this->property_id,
                'property_name' => $this->property->name ?? 'Desconocido',
            ],

            // NUEVO: Categoría Relacional
            'category' => [
                'id' => $this->category_id,
                'name' => $this->category?->name ?? 'Sin Categoría',
                'icon' => $this->category?->icon,
            ],

            'batch_id' => $this->batch_id,

            'provider' => $this->provider ? [
                'provider_id' => $this->provider->id,
                'name' => $this->provider->name,
                'tax_id' => $this->provider->tax_id, 
                'email' => $this->provider->email,
                'phone' => $this->provider->phone,
                'contact_name' => $this->provider->contact_name,
            ] : null,

            'assigned_to' => $this->member ? [
                'member_id' => $this->member->id,
                'tm_id' => $this->member->tm_id,
                'name' => $this->member->name,
                'last_name' => $this->member->last_name,
                'full_name' => $this->member->full_name,
                'email' => $this->member->email,
                'position' => $this->member->position_name, // Del accesor del modelo Member
                'department' => $this->member->department_name,
            ] : null,
            
            'info' => [
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
            'price' => (float) $this->price, // NUEVO
            
            'dates' => [
                'purchase' => $this->purchase_date?->format('Y-m-d'),
                'warranty' => $this->warranty_expiry?->format('Y-m-d'),
            ],

            'accessories' => $this->whenLoaded('accessories', function () {
                return $this->accessories->map(function($acc) {
                    return [
                        'id' => $acc->id,
                        'type' => $acc->type,
                        'brand' => $acc->brand,
                        'serial_number' => $acc->serial_number,
                    ];
                });
            }),

            'specs' => [
                'ram' => data_get($specs, 'ram'),
                'storage' => data_get($specs, 'storage'),
                'processor' => data_get($specs, 'processor'),
                
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