<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    
    public function register(Request $request)
    {
        try {
            // Validação dos dados de entrada
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'password.confirmed' => 'A confirmação de senha não coincide com a senha.', // Mensagem personalizada
            ]);

            // Criação do usuário
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Geração do token JWT
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'user' => $user,
                'token' => $token,
            ], 201); // HTTP 201 Created
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retorna erro de validação
            return response()->json([
                'error' => 'Erro de validação',
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422); // HTTP 422 Unprocessable Entity
        } catch (\Exception $e) {
            // Captura outros erros e retorna uma mensagem genérica
            return response()->json([
                'error' => 'Erro ao registrar usuário',
                'message' => $e->getMessage(),
            ], 500); // HTTP 500 Internal Server Error
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Credenciais inválidas'], 401); // Unauthorized
            }

            return response()->json([
                'token' => $token,
            ], 200); // OK
        } catch (\Exception $e) {
            // Capturar erros inesperados
            return response()->json([
                'error' => 'Erro ao realizar login',
                'message' => $e->getMessage(),
            ], 500); // Internal Server Error
        }
    }

    public function logout()
    {
        try {
            Auth::logout();

            return response()->json(['message' => 'Logout realizado com sucesso'], 200); // OK
        } catch (\Exception $e) {
            // Capturar erros inesperados
            return response()->json([
                'error' => 'Erro ao realizar logout',
                'message' => $e->getMessage(),
            ], 500); // Internal Server Error
        }
    }

    public function profile()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'error' => 'Usuário não autenticado',
                ], 401); // Unauthorized
            }

            return response()->json($user, 200); // OK
        } catch (\Exception $e) {
            // Capturar erros inesperados
            return response()->json([
                'error' => 'Erro ao obter perfil do usuário',
                'message' => $e->getMessage(),
            ], 500); // Internal Server Error
        }
    }

}
