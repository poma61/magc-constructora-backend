<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use App\Models\Desarrolladora;
use Illuminate\Http\Request;
use Throwable;

class ClienteController extends Controller
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

            $cliente = Cliente::join('desarrolladoras', 'desarrolladoras.id', '=', 'clientes.id_desarrolladora')
                ->select(
                    'clientes.*',
                )
                ->where('clientes.status', true)
                ->where('desarrolladoras.status', true)
                ->where('desarrolladoras.id', $desarrolladora->id)
                ->get();


            return response()->json([
                'records' => $cliente,
                'status' => true,
                'message' => "OK",
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'records' => null,
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function store(ClienteRequest $request)
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

            $cliente = new Cliente($request->all());
            $cliente->id_desarrolladora = $desarrolladora->id;
            $cliente->status = true;
            $cliente->save();


            return response()->json([
                'record' => $cliente,
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



    public function update(ClienteRequest $request)
    {
        try {
            //no es necesario verificar la desarrolladora porque  estamos editando un registro existente 
            $cliente = Cliente::where('status', true)
                ->where('id', $request->input('id'))
                ->first();

            $cliente->update($request->all());
            return response()->json([
                'record' => $cliente,
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
            $cliente = Cliente::where('status', true)
                ->where('id', $request->input('id'))
                ->first();
            $cliente->status = false;
            $cliente->update();
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

            $cliente = Cliente::join('desarrolladoras', 'desarrolladoras.id', '=', 'clientes.id_desarrolladora')
                ->select(
                    'clientes.*',
                )
                ->where('clientes.status', true)
                ->where('clientes.ci', $request->input('ci'))
                ->where('desarrolladoras.status', true)
                ->where('desarrolladoras.id', $desarrolladora->id)
                ->first();


            if ($cliente == null) {
                return response()->json([
                    'record' => null,
                    'status' => false,
                    'message' => "No se encontro el cliente con Carnet de Identidad numero {$request->input('ci')}, y/o 
                    el cliente no pertenece a la desarrolladora {$desarrolladora->nombres}.",
                ], 404);
            } else {
                return response()->json([
                    'record' => $cliente,
                    'status' => true,
                    'message' => "Cliente con Carnet de Identidad numero {$request->input('ci')}.",
                ], 200);
            }
        } catch (Throwable $th) {
            return response()->json([
                'record' => null,
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}//class
