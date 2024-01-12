<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//add
use App\Http\Requests\UsuarioByRoleRequest;
use App\Models\Role;
use App\Models\Desarrolladora;
use App\Models\Usuario;
use App\Models\UsuarioRole;
use Illuminate\Support\Facades\Hash;
use Throwable;


class UsuarioController extends Controller
{

    public function index(Request $request)
    {
        try {
            $desarrolladora = Desarrolladora::where('status', true)
                ->where('nombres', $request->input('desarrolladora'))
                ->first();
            //debemos verificar si la desarrolladora existe en la base de datos por seguridad y estabilidad del sistema
            if ($desarrolladora == null) {
                return response()->json([
                    'records' => null,
                    'status' => false,
                    'message' => "No se encontro la desarrolladora {$request->input('desarrolladora')}",
                ], 404);
            }

            $user = Usuario::join('usuario_roles', 'usuario_roles.id_user', '=', 'usuarios.id')
                ->join('roles', 'roles.id', '=', 'usuario_roles.id_role')
                ->join('personals', 'personals.id', '=', 'usuarios.id_personal')
                ->join('desarrolladoras', 'desarrolladoras.id', '=', 'personals.id_desarrolladora')
                ->select(
                    'usuarios.*',
                    'roles.rol_name',
                    'personals.nombres',
                    'personals.apellido_paterno',
                    'personals.apellido_materno',
                    'personals.foto',
                )
                ->where('personals.status', true)
                ->where('usuarios.user','<>','system')
                ->where('usuarios.status', true)
                ->where('usuario_roles.status', true)
                ->where('desarrolladoras.status', true)
                ->where('desarrolladoras.id', $desarrolladora->id)
                ->get();

            return response()->json([
                'records' => $user,
                'status' => true,
                'message' => 'OK',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'records' => null,
                'status' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function store(UsuarioByRoleRequest $request)
    {
        try {
            $desarrolladora = Desarrolladora::where('status', true)
                ->where('nombres', $request->input('desarrolladora'))
                ->first();
            //debemos verificar si la desarrolladora existe en la base de datos por seguridad y estabilidad del sistema
            if ($desarrolladora == null) {
                return response()->json([
                    'status' => false,
                    'message' => "No se encontro la desarrolladora {$request->input('desarrolladora')}",
                ], 404);
            }

            $role = Role::where('rol_name', $request->input('rol_name'))
                ->first();
            //  verificar si el rol existe por seguridad y estabilidad del sistema
            if ($role == null) {
                return response()->json([
                    'status' => false,
                    'message' => "No se encontro el rol {$request->input('rol_name')}",
                ], 404);
            }

            $usuario = new Usuario();
            $usuario->user = $request->input('user');
            $usuario->password = Hash::make($request->input('password'));
            $usuario->status = true;
            $usuario->id_personal = $request->input('id_personal');
            $usuario->save();

            //creamos el rol 
            $usuario_role = new UsuarioRole();
            $usuario_role->id_role = $role->id;
            $usuario_role->id_user = $usuario->id;
            $usuario_role->status = true;
            $usuario_role->save();

            $user = Usuario::join('usuario_roles', 'usuario_roles.id_user', '=', 'usuarios.id')
                ->join('roles', 'roles.id', '=', 'usuario_roles.id_role')
                ->join('personals', 'personals.id', '=', 'usuarios.id_personal')
                ->join('desarrolladoras', 'desarrolladoras.id', '=', 'personals.id_desarrolladora')
                ->select(
                    'usuarios.*',
                    'roles.rol_name',
                    'personals.nombres',
                    'personals.apellido_paterno',
                    'personals.apellido_materno',
                    'personals.foto',
                ) // no es necesario verificar los status de las tablas porque se supone que es un registro nuevo por lo tanto ya anteriormente se verifico que el registro tiene un status true
                ->where('usuarios.id', $usuario->id)
                ->first();

            return response()->json([
                'record' => $user,
                'status' => true,
                'message' => 'Registro creado!',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'record' => null,
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }



    public function update(UsuarioByRoleRequest $request)
    {
        try {
            $role = Role::where('rol_name', $request->input('rol_name'))
                ->first();
            //  verificar si el rol existe por seguridad y estabilidad del sistema
            if ($role == null) {
                return response()->json([
                    'status' => false,
                    'message' => "No se encontro el rol {$request->input('rol_name')}",
                ], 404);
            }

            $usuario = Usuario::where('status', true)
                ->where('id', $request->input('id'))
                ->first();


            //verificamos si el registro se encuentra por estabilidad del sistema
            if ($usuario == null) {
                return response()->json([
                    'record' => null,
                    'status' => false,
                    'message' => 'Este registro no se encuentra en el sistema!',
                ], 404);
            }


            $usuario->user = $request->input('user');
            $usuario->id_personal = $request->input('id_personal');

            //Si el campo password  NO esta vacio entonces empty devolvera false y se encriptara la contraseña
            if (empty($request->input('password')) == false) {
                $usuario->password = Hash::make($request->input('password'));
            }
            $usuario->update();


            //actualizamos el rol 
            $usuario_role = UsuarioRole::where('status', true)
                ->where('id_user', $usuario->id)
                ->first();
            $usuario_role->id_role = $role->id;
            $usuario_role->update();

            $user = Usuario::join('usuario_roles', 'usuario_roles.id_user', '=', 'usuarios.id')
                ->join('roles', 'roles.id', '=', 'usuario_roles.id_role')
                ->join('personals', 'personals.id', '=', 'usuarios.id_personal')
                ->join('desarrolladoras', 'desarrolladoras.id', '=', 'personals.id_desarrolladora')
                ->select(
                    'usuarios.*',
                    'roles.rol_name',
                    'personals.nombres',
                    'personals.apellido_paterno',
                    'personals.apellido_materno',
                    'personals.foto',
                ) // no es necesario verificar los status de las tablas porque se supone que es un registro editato y ya se veririco el status anteriormente segun el codigo
                ->where('usuarios.id', $usuario->id)
                ->first();


            return response()->json([
                'record' => $user,
                'status' => true,
                'message' => 'Registro actualizado!',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'record' => null,
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function destroy(Request $request)
    {
        try {
            $usuario = Usuario::where('status', true)
                ->where('id', $request->input('id'))
                ->first();

            //verificamos si el registro se encuentra por estabilidad del sistema
            if ($usuario == null) {
                return response()->json([
                    'record' => null,
                    'status' => false,
                    'message' => 'Este registro no se encuentra en el sistema!',
                ], 404);
            }

            $usuario->status = false;
            $usuario->update();

            return response()->json([
                'status' => true,
                'message' => 'Registro eliminado!',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}//class
