<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceLogRequest extends FormRequest
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
            'asset_id' => 'required|integer|exists:assets,id',
            'event_type' => 'required|string|in:falla,mantenimiento_preventivo,asignacion,baja,otro',
            'title' => 'nullable|string|max:150',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'event_date' => 'required|date',
            'resolved_date' => 'nullable|date|after_or_equal:event_date',
        ];
    }
}
