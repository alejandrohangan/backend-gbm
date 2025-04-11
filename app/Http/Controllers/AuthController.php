<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Registro de usuario
    public function register(Request $request)
    {
        // Validación de datos con validate() (más limpio y directo)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',  // Aseguramos que la contraseña sea confirmada
        ]);

        // Crear al usuario con datos validados
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Intentamos generar el token JWT
        try {
            $token = JWTAuth::fromUser($user);
            return response()->json(['token' => $token], 201); // Código 201 para recurso creado
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo generar el token'], 500);
        }
    }

    // Login de usuario
    public function login(Request $request)
    {
        // Validación de credenciales
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Intentamos generar el token JWT para el usuario
        try {
            if ($token = JWTAuth::attempt($credentials)) {
                return response()->json(['token' => $token], 200); // 200 OK
            } else {
                return response()->json(['error' => 'Credenciales incorrectas'], 401); // 401 Unauthorized
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo crear el token'], 500); // 500 Internal Server Error
        }
    }
}