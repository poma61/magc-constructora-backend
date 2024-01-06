<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Throwable;

class MultipagoController extends Controller
{

    public function store(Request $request)
    {
        //
    }


    public function show(string $id)
    {
        //
    }


    public function update(Request $request)
    {
        //
    }

    public function recordByCi(Request $request)
    {
        try {

            $cliente = Cliente::join('clientes_has_contratos', 'clientes_has_contratos.id_cliente', '=', 'clientes.id')
                ->join('contratos', 'contratos.id', '=', 'clientes_has_contratos.id_contrato')
                ->select(
                    'clientes.nombres',
                    'clientes.apellido_paterno',
                    'clientes.apellido_materno',
                    'contratos.n_contrato',
                )
                ->where('clientes.status', true)
                ->where('contratos.status', true)
                ->where('clientes.ci', $request->input('ci'))
                ->get();

            if ($cliente->count() > 0) {
                return response()->json([
                    'record' => $cliente,
                    'status' => true,
                    'message' => "Cliente con Carnet de Identidad numero {$request->input('ci')}.",
                ], 200);
            } else {
                return response()->json([
                    'record' => null,
                    'status' => false,
                    'message' => "No se encontro el cliente con Carnet de Identidad numero {$request->input('ci')}"
                ], 404);
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
