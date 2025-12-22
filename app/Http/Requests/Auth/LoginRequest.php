<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            /**
             * El correo corporativo del usuario.
             * @example admin@hilton.com
             */
            'email' => 'required|email',
            /**
             * La contraseña del sistema.
             * @example superuser-itam
             */
            'password' => 'required|string|min:8',
        ];
    }
}
