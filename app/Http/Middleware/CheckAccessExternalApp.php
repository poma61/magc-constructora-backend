<?php

namespace App\Http\Middleware;

use App\Models\AccessExternalApp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CheckAccessExternalApp
{


    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (empty($request->header('Access-Token'))) {
                return response()->json([
                    'message' => 'No se encontro ningun token de acceso!',
                    'status' => false,
                ], 403);
            }

            $access_external_app = AccessExternalApp::where('access_token', $request->header('Access-Token'))
                ->first();
            if ($access_external_app == null) {
                return response()->json([
                    'message' => 'El token de acceso no es válido',
                    'status' => false,
                ], 403);
            } else {
                return $next($request);
            }
        } catch (Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'status' => false,
            ], 500);
        }
    }
}//class
