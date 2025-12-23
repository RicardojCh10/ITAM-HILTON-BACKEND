<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => 'sometimes|integer|exists:properties,id',
            'tm_id' => 'nullable|string|max:50',
            'name' => 'sometimes|string|max:150',
            'email' => 'nullable|email|max:150',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'onq_id' => 'nullable|string|max:50',
            'status' => 'sometimes|string|max:20',
            
            // Permitimos actualizar detalles parciales
            'details' => 'nullable|array',
            'details.phone' => 'nullable|string',
            'details.notes' => 'nullable|string',
        ];
    }
}
