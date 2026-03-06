<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlatformPermissionRequest extends FormRequest
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
                // Evita duplicar el nombre DENTRO de la misma plataforma
                Rule::unique('platform_permissions')->where(function ($query) {
                    return $query->where('platform_id', $this->platform_id);
                })
            ],
            'description' => 'nullable|string|max:255',
        ];
    }
}