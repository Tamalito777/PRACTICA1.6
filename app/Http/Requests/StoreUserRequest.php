<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'rol' => 'required|in:administrador,jugador',
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => 'El campo es obligatorio.',
            'email.email' => 'El correo debe tener un formato válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'rol.in' => 'El rol seleccionado no es válido.',
        ];
    }
}
