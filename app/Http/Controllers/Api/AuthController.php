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
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = ['user' => $request->input('user'), 'password' => $request->input('password'), 'status' => true];
            if (!$token = Auth::attempt($credentials)) {
                return response()->json([
                    "session_auth" => [
                        "access_token" => null,
                        "token_type" => null,
                        "time_expiration_token" => null,
                        "role" => null,
                    ],
                    'message' => 'Usuario y/o contraseña incorrectos.',
                    "false" => false,
                ], 200);
            }

            return response()->json([
                "session_auth" => [
                    "access_token" => $token,
                    "type_token" => "Bearer",
                    "time_expiration_token" => Auth::factory()->getTTL(),
                    "role" => $this->role(),
                ],
                "message" => "Sesion iniciada.",
                "status" => true,
            ]);

        } catch (Throwable $th) {
            return response()->json([
                'access_token' => null,
                'message' => $th->getMessage(),
                'status' => false,
            ], 500);
        }
    }

    public function logout(): JsonResponse
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
    public function me(): JsonResponse
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

    public function updateCredentials(AuthProfileRequest $request): JsonResponse
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

    public function role(): string
    {
        return Auth::user()->isPersonal()->rol_name;
    }

    public function authByDesarrolladora()
    {
        try {
            $user = Auth::user()->isPersonal();
            return response()->json([
                'record' => [
                    'role' => $user->rol_name,
                    'desarrolladora' => $user->desarrolladora
                ],
                'message' => 'OK',
                'status' => true,
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'record' => [
                    'role' => null,
                    'desarrolladora' => null
                ],
                'message' => $th->getMessage(),
                'status' => false,
            ], 500);
        }
    }

} //class
