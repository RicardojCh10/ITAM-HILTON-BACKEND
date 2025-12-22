<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permitimos acceso (luego puedes restringir por rol)
    }

    public function rules() : array
    {
        return [
            // Validaciones Estándar
            'property_id'   => 'required|exists:properties,id',
            'category'      => 'required|string', // laptop, mobile, switch, etc.
            
            // Validaciones flexibles según tu SQL
            'serial_number' => 'nullable|string|unique:assets,serial_number', 

            'hilton_name'   => 'nullable|string',
            'mac_address'   => 'nullable|mac_address',
            'ip_address' => 'nullable|ipv4',
            'status'        => 'required|string', // active, repair, etc.
            'brand'         => 'nullable|string',
            'model'         => 'nullable|string',
            
            // Fechas
            'purchase_date'   => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            
            // Datos JSON dinámicos
            'specs' => 'nullable|array',
            
        ];
    }
}