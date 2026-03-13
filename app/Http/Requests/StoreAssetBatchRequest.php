<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules() : array
    {
        return [
            // LOTE / BATCH
            'property_id' => 'required|integer|exists:properties,id',
            'category_id' => 'required|integer|exists:asset_categories,id', // Nueva tabla
            'quantity'    => 'required|integer|min:1|max:100', // Clave para creación masiva
            'price'       => 'nullable|numeric|min:0', // Precio unitario

            // DATOS BÁSICOS (Aplicarán a todos los activos del lote)
            'provider_id'   => 'nullable|integer|exists:providers,id',
            'brand'         => 'nullable|string|max:50',
            'model'         => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date|after_or_equal:purchase_date',
            'status'        => 'required|string|in:active,repair,lost,retired,stored',

            // SPECS TÉCNICOS GLOBALES (RAM, CPU, etc)
            'specs' => 'nullable|array',
            'specs.ram' => 'nullable|string',
            'specs.storage' => 'nullable|string',
            'specs.processor' => 'nullable|string',

            // ACCESORIOS GLOBALES (Se crearán para cada activo del lote)
            'accessories_base'   => 'nullable|array',
            'accessories_base.*.type' => 'required_with:accessories_base|string|max:50', // Ej: Cargador, Mouse
            'accessories_base.*.brand' => 'nullable|string|max:50',

            // SERIALES ESPECÍFICOS (Si applies, debe enviar un array del tamaño de quantity)
            'serials'   => 'nullable|array',
            'serials.*' => 'nullable|string|distinct|unique:assets,serial_number',
        ];
    }
}