<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthProfileRequest;
use App\Models\Usuario;
use Illuminate\Http\Request;
//add
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthController extends Controller
{

    public function  login(Request $request)
    {
        try {
            $credentials = ['user' => $request->input('user'), 'password' => $request->input('password'), 'status' => true];
            if (!$token = Auth::attempt($credentials)) {
                return response()->json([
                    'access_token' => null,
                    'message' => 'Usuario y/o contraseña incorrectos.',
                    'status' => false,
                ], 200);
            }
            return $this->respondWithToken($token);
        } catch (Throwable $th) {
            return response()->json([
                'access_token' => null,
                'message' => $th->getMessage(),
                'status' => false,
            ], 500);
        }
    }

    public function respondWithToken($token)
    {
        return response()->json([
            "access_token" => $token,
            "token_type" => "Bearer",
            "message" => "Sesion iniciada.",
            "expires_in" => Auth::factory()->getTTL(),
            "status" => true,
        ]);
    }

    public function logout()
    {
        try {
            Auth::logout();
            return response()->json([
                'message' => 'Successfully logged out',
                'status' => true,
            ], 200);
        } catch (Throwable $th) {

            return response()->json([
                'message' => $th->getMessage(),
                'status' => false,
            ], 500);
        }
    }
    public function me()
    {
        try {
            return response()->json([
                'record' => Auth::user()->isPersonal(),
                'status' => true,
                'message' => 'OK',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'record' => null,
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function updateCredentials(AuthProfileRequest $request)
    {
        try {
            $user = Usuario::where('id', Auth::user()->id)
                ->where('status', true)
                ->first();

            if ($user == null) {
                return response()->json([
                    'record' => $user,
                    'message' => 'No se pudo encontrar el usuario!',
                    'status' => false,
                ], 404);
            } else {
                $user->user = $request->input('user');
                $user->password = Hash::make($request->input('new_password'));
                $user->update();
                return response()->json([
                    'record' => $user,
                    'message' => 'Credenciales actualizados!',
                    'status' => true,
                ], 200);
            }
        } catch (Throwable $th) {
            return response()->json([
                'record' => [],
                'message' => $th->getMessage(),
                'status' => false,
            ], 500);
        }
    }

    public function isRole()
    {
        try {
            $user = Auth::user()->isPersonal();
            return response()->json([
                'record' => ['role' => $user->rol_name, 'desarrolladora' => $user->desarrolladora],
                'message' => 'OK',
                'status' => true,
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'role' => null,
                'message' => $th->getMessage(),
                'status' => false,
            ], 500);
        }
    }
}//class
