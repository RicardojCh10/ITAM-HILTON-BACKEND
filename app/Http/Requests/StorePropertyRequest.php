<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permitimos acceso (luego puedes restringir por rol)
    }

    public function rules() : array
    {
        return [
            'name'        => 'required|string|unique:properties,name',
            'code' => 'required|string|max:50|unique:properties,code',
        ];
    }
}