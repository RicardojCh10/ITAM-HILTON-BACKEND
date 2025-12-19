<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permitimos acceso (luego puedes restringir por rol)
    }

    public function rules()
    {
        return [
            // Validaciones Estándar
            'property_id'   => 'required|exists:properties,id',
            'category'      => 'required|string', // laptop, mobile, switch, etc.
            
            // Validaciones flexibles según tu SQL
            'serial_number' => 'nullable|string|unique:assets,serial_number', 
            'hilton_name'   => 'nullable|string',
            'status'        => 'required|string', // active, repair, etc.
            'brand'         => 'nullable|string',
            'model'         => 'nullable|string',
            
            // Fechas
            'purchase_date'   => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            
            // Nota: No validamos 'imei', 'ram' o 'discoduro' aquí porque son dinámicos
            // y el Service los capturará automáticamente.
        ];
    }
}