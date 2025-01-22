<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            // Tentar autenticar o usuário com o token
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return response()->json([
                'error' => 'O token expirou. Faça login novamente.'
            ], 401); // Unauthorized
        } catch (TokenInvalidException $e) {
            return response()->json([
                'error' => 'O token fornecido é inválido.'
            ], 401); // Unauthorized
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'O token não foi fornecido.'
            ], 401); // Unauthorized
        }

        return $next($request);
    }
}
