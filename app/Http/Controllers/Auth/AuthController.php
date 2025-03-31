<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:20|regex:/^[a-zA-z0-9_\\.]+$/|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|max:24|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\.\*!#"$%&\\/\\=?¿¡])(?=.*[a-zA-Z]).+$/',
            'birth_date' => 'required|date|before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d'),
        ], [
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'birth_date' => $request->birth_date
        ]);

        $user->birth_date = $user->birth_date->format('Y-m-d');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], $status=201);
    }
}
