<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtenemos el ID de la ruta para ignorarlo en la validación unique
        // Ajusta 'provider' según tu definición de ruta (ej: /providers/{id} o /providers/{provider})
        $providerId = $this->route('id') ?? $this->route('provider'); 

        return [
            // --- DATOS EMPRESA ---
            'name' => 'sometimes|string|max:255',
            
            'legal_name' => 'nullable|string|max:255',
            
            // Ignoramos el ID actual al validar único
            'tax_id' => [
                'nullable', 
                'string', 
                'max:20', 
                Rule::unique('providers', 'tax_id')->ignore($providerId)
            ],

            'address' => 'nullable|string',

            // --- CONTACTO GENERAL ---
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',

            // --- REPRESENTANTE ---
            'contact_name' => 'nullable|string|max:255',
            'contact_position' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
        ];
    }
}