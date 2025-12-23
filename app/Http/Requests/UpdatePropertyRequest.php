<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permitimos acceso (luego puedes restringir por rol)
    }

    public function rules() : array
    {
      return [
           
            'name' => 'sometimes|string|max:100',
            'code' => 'sometimes|string|max:50|unique:properties,code,' . $this->route('property'),
        ];
    }
}