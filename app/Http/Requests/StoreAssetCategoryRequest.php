<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreAssetCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    // Generar el slug automáticamente antes de validar
    protected function prepareForValidation()
    {
        if ($this->has('name')) {
            $this->merge(['slug' => Str::slug($this->name)]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:asset_categories,name',
            'slug' => 'required|string|max:100|unique:asset_categories,slug',
            'prefix' => 'required|string|max:10', // Ej: LT, DK, PH
            'icon' => 'nullable|string|max:50', // Ej: pi-laptop
            'is_serialized' => 'boolean',
            'has_network_fields' => 'boolean',
        ];
    }
}