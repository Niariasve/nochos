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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => 'required|string',
            'password' => 'required|string'
        ];
    }

    public function messages(): array 
    {
        return [
            'login.required' => 'Debes ingresar tu nombre de usuario o email.',
            'password.required' => 'La contraseña es obligatoria'
        ];
    }

    public function credentials() {
        $login = $this->input('login');

        return filter_var($login, FILTER_VALIDATE_EMAIL)
            ? ['email' => $login, 'password' => $this->input('password')]
            : ['name' => $login, 'password' => $this->input('password')];
    }
}
