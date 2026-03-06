<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'department_id' => 'sometimes|integer|exists:departments,id',
            'name' => 'sometimes|string|max:100|unique:positions,name',
            'create_at' => 'nullable|date',

            'default_permissions'   => 'nullable|array',
            'default_permissions.*' => 'integer|exists:platform_permissions,id',
        ];
    }
}
