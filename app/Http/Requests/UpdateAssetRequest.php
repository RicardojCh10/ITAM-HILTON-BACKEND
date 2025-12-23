<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => 'sometimes|integer|exists:properties,id',
            'member_id'   => 'nullable|integer|exists:members,id',
            'category'    => 'sometimes|string|max:50',
            'brand'       => 'nullable|string|max:50',
            'model'       => 'nullable|string|max:100',
            // Ignora el serial actual para no dar error de "ya existe"
            'serial_number' => 'nullable|string|max:100|unique:assets,serial_number,' . $this->route('asset'),
            'hilton_name' => 'nullable|string|max:100',
            'mac_address' => 'nullable|mac_address',
            'ip_address'  => 'nullable|ipv4',
            'status'      => 'sometimes|string|in:active,repair,lost,retired,stored',
            'purchase_date'   => 'nullable|date',
            'warranty_expiry' => 'nullable|date|after_or_equal:purchase_date',
            'specs' => 'nullable|array',
        ];
    }
}
