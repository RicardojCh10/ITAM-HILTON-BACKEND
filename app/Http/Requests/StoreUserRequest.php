<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email|max:150',
            'password' => 'required|string|min:6', // Mínimo 6 caracteres
            'role' => 'nullable|string|max:50',
            'property_id' => 'nullable|exists:properties,id',
        ];
    }
}
