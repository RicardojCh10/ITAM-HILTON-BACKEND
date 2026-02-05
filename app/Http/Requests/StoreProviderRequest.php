<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // --- DATOS EMPRESA ---
            'name' => 'required|string|max:255',
            
            'legal_name' => 'nullable|string|max:255',
            
            'tax_id' => 'nullable|string|max:20|unique:providers,tax_id',

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