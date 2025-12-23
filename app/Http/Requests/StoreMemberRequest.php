<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
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
            // --- VINCULACIÓN ---
            'property_id' => 'required|integer|exists:properties,id',

            // --- DATOS PRINCIPALES ---
            'tm_id' => 'nullable|string|max:50',

            'name' => 'required|string|max:150',

            'email' => 'nullable|email|max:150',

            'position' => 'nullable|string|max:100',

            'department' => 'nullable|string|max:100',
           
            'onq_id' => 'nullable|string|max:50',

            'status' => 'nullable|string|max:20',

            // --- JSON DETAILS (Flexible) ---
            'details' => 'nullable|array',

            'details.phone' => 'nullable|string',

            'details.notes' => 'nullable|string'
        ];
    }
}
