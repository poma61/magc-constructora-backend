<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        try {
            if (Auth::user()->hasRole($roles)) {
                return $next($request);
            } else {
                return response()->json([
                    'message' => 'No tienes permisos para realizar esta operación!',
                    'status' => false,
                ], 403);
            }
        } catch (Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'status' => false,
            ], 500);
        }
    }
}//class
