<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlatformRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Ignoramos el ID actual para que no marque error de "nombre duplicado" al actualizar
            'name' => 'required|string|max:100|unique:platforms,name,' . $this->route('platform'),
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }
}