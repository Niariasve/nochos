<?php

namespace App\Http\Requests\Auth;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    //protected $stopOnFirstFailure = true;

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
            'name' => 'required|string|min:3|max:20|regex:/^[a-zA-z0-9_\\.]+$/|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|max:24|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\.\*!#"$%&\\/\\=?¿¡])(?=.*[a-zA-Z]).+$/',
            'birth_date' => 'required|date|before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d')
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de usuario es obligatorio.',
            'name.min' => 'El nombre de usuario debe tener mínimo 3 caracteres.',
            'name.max' => 'El nombre de usuario debe tener máximo 20 caracteres.',
            'name.regex' => 'El nombre de usuario no debe contener caracteres especiales.',
            'name.unique' => 'El nombre su usuario ya existe.',

            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser válido.' ,
            'email.unique' => 'Ese email ya ha sido utilizado por otro usuario.',
            
            'password.required' => 'La contraseña es oblligatoria.',
            'password.min' => 'La contraseña debe contener al menos 6 caracteres.',
            'password.max' => 'La contraseña debe contener máximo 24 caracteres.',
            'password.regex' => 'La contraseña debe contener letras minúsuclas y mayúsculas, números y caracteres especiales.',
            
            'birth_date.required' => 'La fecha de nacimiento es obligatoria.',
            'birth_date.date' => 'La fecha debe tener un formato válido',
            'birth_date.before_or_equal' => 'El usuario debe ser mayor de edad.'
        ];
    }
}
