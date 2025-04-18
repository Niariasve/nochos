<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    function register(RegisterRequest $request) {
        $validated = $request->validated();
        
        if ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'birth_date' => $validated['birth_date']
            ]);
            
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token
            ], 201);

        } else {
            return response()->json([
                'errors' => $request->errors()
            ], 400);
        }
    }

    function login(LoginRequest $request) {
        $validated = $request->validated();

        if ($validated) {
            $credentials = $request->credentials();

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Nombre de usuario/email o contraseña incorrectos' 
                ], 400);
            }
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
    }

    function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Has cerrado sesión con éxito'
        ], 200);
    }

    function changePassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required|string',
            'newPassword' => 'required|string|min:6|max:24|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\.\*!#"$%&\\/\\=?¿¡])(?=.*[a-zA-Z]).+$/',
            'newPasswordAgain' => 'required|string|same:newPassword'
        ],[
            'oldPassword.required' => 'La contraseña anterior es obligatoria.',

            'newPassword.required' => 'La contraseña nueva es oblligatoria.',
            'newPassword.min' => 'La contraseña debe contener al menos 6 caracteres.',
            'newPassword.max' => 'La contraseña debe contener máximo 24 caracteres.',
            'newPassword.regex' => 'La contraseña debe contener letras minúsuclas y mayúsculas, números y caracteres especiales.',

            'newPasswordAgain.same' => 'Las contraseñas no coinciden'
        ]);

        // Sorprendentemente laravel hace esto por defecto
        // if ($validator->fails()) {
        //     return response()->json($validator->validated());
        // }

        $message = '';
        $errors = [];
        $status = 202;

        if (!Hash::check($request->oldPassword, $request->user()->password)) {
            $message = 'Contraseña Incorrecta.';
            $errors['oldPassword'][] = 'Contraseña incorrecta';
        }
        
        $newPassword = $validator->validated()['newPassword'];

        if (Hash::check($newPassword, $request->user()->password)) {
            $message = 'Contraseña nueva inválida';
            $errors['newPassword'][] = 'Contraseña inválida';
        }
        
        if (!empty($errors)) {
            $status = 422;
            return response()->json([
                'message' => $message, 
                'errors' => $errors
            ], $status);
        }

        return response()->json([
            'message' => 'Contraseña actualizada con éxito.'
        ], 202);
    }
}
