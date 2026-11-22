<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AuthLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => 'El campo es obligatorio.',
            'email.email' => 'El correo debe tener un formato válido.',
            'email.max' => 'El correo no debe exceder los 255 caracteres.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }
}
