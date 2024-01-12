<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
//add
use Tymon\JWTAuth\Facades\JWTAuth;
use Throwable;

class JwtAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        try {

            JWTAuth::parseToken()->authenticate();
            return $next($request);
        } catch (Throwable $th) {
            if ($th instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json([
                    'message' => 'El token no es valido.',
                    'status' => false,
                ], 401);
            } else if ($th instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json([
                    'message' => 'El token ha caducado.',
                    'status' => false,
                ], 401);
            } else {
                return response()->json([
                    'message' => 'Token de autorización no encontrado.',
                    'status' => false,
                ], 401);
            }
        }
    }
}//class
