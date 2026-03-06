<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => 'nullable|integer|exists:properties,id',
            'provider_id' => 'nullable|integer|exists:providers,id',
            'member_id'   => 'nullable|integer|exists:members,id',
            'category_id' => 'nullable|integer|exists:asset_categories,id', // Actualizado
            'brand'       => 'nullable|string|max:50',
            'model'       => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100|unique:assets,serial_number,' . $this->route('asset'),
            'hilton_name' => 'nullable|string|max:100',
            'mac_address' => 'nullable|mac_address',
            'ip_address'  => 'nullable|ipv4',
            'status'      => 'sometimes|string|in:active,repair,lost,retired,stored',
            'price'       => 'nullable|numeric|min:0',
            'purchase_date'   => 'nullable|date',
            'warranty_expiry' => 'nullable|date|after_or_equal:purchase_date',

            'accessories_base'   => 'nullable|array',
            'accessories_base.*.type' => 'required_with:accessories_base|string|max:50',
            'accessories_base.*.brand' => 'nullable|string|max:50',
            'accessories_base.*.serial_number' => 'nullable|string|max:100',

            // SPECS
            'specs' => 'nullable|array',
            'specs' => 'nullable|array',
            'specs.ram' => 'nullable|string',
            'specs.storage' => 'nullable|string',
            'specs.processor' => 'nullable|string',

            // CAMPOS MÓVILES 
            'specs.imei' => 'nullable|string|max:50',
            'specs.sim' => 'nullable|string|max:50',
            'specs.plan' => 'nullable|string|max:100',
            'specs.phone_number' => 'nullable|string|max:20',
            'specs.carrier' => 'nullable|string|max:50',
            'specs.description' => 'nullable|string',
        ];
    }
}
