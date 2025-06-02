<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
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
                // Obtener el usuario autenticado
                $user = JWTAuth::user();
                // Devolver el token y los datos del usuario
                return response()->json([
                    'token' => $token,
                    'user' => $user
                ], 200); // 200 OK
            } else {
                return response()->json(['error' => 'Credenciales incorrectas'], 401); // 401 Unauthorized
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo crear el token'], 500); // 500 Internal Server Error
        }
    }
    //funcion de logout cechar el frontend
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada correctamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo cerrar la sesión',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function me()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json([
                'user' => $user,
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 401);
        }
    }
}
