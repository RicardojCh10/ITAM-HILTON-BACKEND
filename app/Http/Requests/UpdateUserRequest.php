<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => 'sometimes|string|max:150',
            'email' => [
                'sometimes', 
                'email', 
                'max:150', 
                // IMPORTANTE: Ignorar el ID actual para que no falle si no cambias el email
                Rule::unique('users')->ignore($userId) 
            ],
            'password' => 'nullable|string|min:6', // Opcional al editar
            'role' => 'nullable|string|max:50',
            'property_id' => 'nullable|exists:properties,id',
        ];
    }
}
