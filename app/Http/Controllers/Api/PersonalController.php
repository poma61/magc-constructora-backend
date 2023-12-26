<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//add
use App\Http\Requests\PersonalRequest;
use App\Models\Desarrolladora;
use App\Models\Personal;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PersonalController extends Controller
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

            $personal = Personal::join('desarrolladoras', 'desarrolladoras.id', '=', 'personals.id_desarrolladora')
                ->select('personals.*')
                ->where('desarrolladoras.status', true)
                ->where('personals.status', true)
                ->where('desarrolladoras.id', $desarrolladora->id)
                ->get();

            return response()->json([
                'records' => $personal,
                'status' => true,
                'message' => 'OK',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'records' => null,
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function store(PersonalRequest $request)
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

            $personal = new Personal($request->all());
            $image_path = $request->file('foto')->store('img/personal', 'public');
            $personal->foto = "/storage/{$image_path}";
            $personal->id_desarrolladora = $desarrolladora->id;
            $personal->status = true;
            $personal->save();

            return response()->json([
                'record' => $personal,
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
    } //store


    public function update(PersonalRequest $request)
    {
        try {
            $personal = Personal::where('status', true)->where('id', $request->input('id'))->first();
            $personal->fill($request->except('foto'));
            //verificar si subio una nueva imagen
            if ($request->file('foto') != null) {
                Storage::disk('public')->delete(str_replace("/storage", "", $personal->foto));
                $image_path = $request->file('foto')->store('img/personal', 'public');
                $personal->foto = "/storage/{$image_path}";
            }
            $personal->update();

            return response()->json([
                'record' => $personal,
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
    } //update


    public function destroy(Request $request)
    {
        try {
            $personal = Personal::where('status', true)->where('id', $request->input('id'))->first();
            $personal->status = false;
            $personal->update();

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

    public function recordByCi(Request $request)
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

            $personal = Personal::join('desarrolladoras', 'desarrolladoras.id', '=', 'personals.id_desarrolladora')
                ->select(
                    'personals.id',
                    'personals.nombres',
                    'personals.apellido_paterno',
                    'personals.apellido_materno',
                )
                ->where('personals.status', true)
                ->where('desarrolladoras.status', true)
                ->where('personals.ci', $request->input('ci'))
                ->where('desarrolladoras.id', $desarrolladora->id)
                ->first();

            if ($personal == null) {
                return response()->json([
                    'record' => null,
                    'message' => "No se encontro ningun personal con CI {$request->input('ci')}, verifique si el personal
                                  se encuentra registrado en el sistema y/o el personal no pertence a la desarrolladora 
                                   {$request->input('desarrolladora')}.",
                    'status' => false,
                ], 404);
            } else {
                return response()->json([
                    'record' => $personal,
                    'message' => "Se encontro un registro con Carnet de Identidad número {$request->input('ci')}.",
                    'status' => true,
                ], 200);
            }
        } catch (Throwable $th) {
            return response()->json([
                'record' => null,
                'message' => $th->getMessage(),
                'status' => false,
            ], 500);
        }
    }
}//class
