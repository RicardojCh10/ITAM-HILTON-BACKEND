<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateAssetCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation()
    {
        if ($this->has('name')) {
            $this->merge(['slug' => Str::slug($this->name)]);
        }
    }

    public function rules(): array
    {
        $id = $this->route('asset_category'); // Ojo: coincidir con el nombre de la ruta

        return [
            'name' => 'sometimes|string|max:100|unique:asset_categories,name,' . $id,
            'slug' => 'sometimes|string|max:100|unique:asset_categories,slug,' . $id,
            'prefix' => 'sometimes|string|max:10',
            'icon' => 'nullable|string|max:50',
            'is_serialized' => 'boolean',
            'has_network_fields' => 'boolean',
        ];
    }
}