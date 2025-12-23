<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaintenanceLogRequest extends FormRequest
{
 
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id' => 'sometimes|integer|exists:assets,id',
            'event_type' => 'sometimes|string|in:falla,mantenimiento_preventivo,asignacion,baja,otro',
            'title' => 'nullable|string|max:150',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'event_date' => 'sometimes|date',
            'resolved_date' => 'nullable|date|after_or_equal:event_date',
        ];
    }
}
