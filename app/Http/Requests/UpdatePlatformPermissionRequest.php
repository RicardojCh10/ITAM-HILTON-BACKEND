<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlatformPermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform_id' => 'required|integer|exists:platforms,id',
            'name' => [
                'required',
                'string',
                'max:100',
                // Excluye el registro actual de la validación
                Rule::unique('platform_permissions')->where(function ($query) {
                    return $query->where('platform_id', $this->platform_id);
                })->ignore($this->route('permission')) 
            ],
            'description' => 'nullable|string|max:255',
        ];
    }
}