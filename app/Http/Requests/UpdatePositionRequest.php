<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePositionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'name' => [
                'required',
                'string',
                'max:100',
                // Magia de Laravel: Ignora el ID de este puesto al buscar duplicados
                Rule::unique('positions')->ignore($this->route('position'))
            ],
            'default_permissions'   => 'nullable|array',
            'default_permissions.*' => 'integer|exists:platform_permissions,id',
        ];
    }
}
