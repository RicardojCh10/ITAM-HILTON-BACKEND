<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id' => 'required|integer|exists:assets,id',
            
            'event_type' => 'required|string|in:repair,warranty,damage,inspection,license,other',
            
            'title' => 'required|string|max:150', // Cambié a required porque suele ser obligatorio
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',    // Cambié a required si es importante para finanzas
            'event_date' => 'required|date',
            'resolved_date' => 'nullable|date|after_or_equal:event_date',
        ];
    }
}