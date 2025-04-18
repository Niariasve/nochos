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
        ]);
    }

    function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Has cerrado sesión con éxito'
        ]);
    }
}
