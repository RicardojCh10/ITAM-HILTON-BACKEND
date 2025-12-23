<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permitimos acceso (luego puedes restringir por rol)
    }

    public function rules() : array
    {
        return [
           // VINCULACIÓN (Vital)
            'property_id' => 'required|integer|exists:properties,id',
            'member_id'   => 'nullable|integer|exists:members,id', // Puede ser null (Stock)

            // DATOS BÁSICOS
            'category'      => 'required|string|max:50',
            'brand'         => 'nullable|string|max:50',
            'model'         => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100|unique:assets,serial_number',
            'hilton_name'   => 'nullable|string|max:100',

            // RED
            'mac_address' => 'nullable|mac_address', // Valida formato MAC real
            'ip_address'  => 'nullable|ipv4',        // Valida IP real

            // ESTADO
            'status' => 'required|string|in:active,repair,lost,retired,stored',
            
            // FECHAS
            'purchase_date'   => 'nullable|date',
            'warranty_expiry' => 'nullable|date|after_or_equal:purchase_date',

            // SPECS (JSON)
            'specs' => 'nullable|array',
            'specs.ram' => 'nullable|string',
            'specs.storage' => 'nullable|string',
            'specs.processor' => 'nullable|string',
        ];
    }
}