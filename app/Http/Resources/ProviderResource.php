<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            // Datos Principales
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'tax_id' => $this->tax_id,
            'address' => $this->address,
            'website' => $this->website,
            
            // Agrupación visual similar a 'corporate_info'
            'company_contact' => [
                'phone' => $this->phone,
                'email' => $this->email,
            ],

            'representative' => [
                'name' => $this->contact_name,
                'position' => $this->contact_position,
                'phone' => $this->contact_phone,
                'email' => $this->contact_email,
            ],
            
            // Metadatos y Relaciones
            'assets_count' => $this->whenCounted('assets'),
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}