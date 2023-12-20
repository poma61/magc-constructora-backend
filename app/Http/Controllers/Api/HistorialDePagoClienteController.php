<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Desarrolladora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class HistorialDePagoClienteController extends Controller
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

            $cliente = Cliente::leftJoin('historial_de_pagos', 'historial_de_pagos.id_cliente', '=', 'clientes.id')
                ->join('desarrolladoras', 'desarrolladoras.id', '=', 'clientes.id_desarrolladora')
                ->select(
                    'clientes.id',
                    'clientes.nombres',
                    'clientes.apellido_paterno',
                    'clientes.apellido_materno',
                   'clientes.correo_electronico',
                    DB::raw('COUNT(historial_de_pagos.id_cliente) as total_pagos'), 
                )
                ->where('clientes.status', true)
                ->where('desarrolladoras.status', true)
                ->where('desarrolladoras.id', $desarrolladora->id)
                //group by para agrupar los clientes y poder contar desarrolladoras.id_cliente
                ->groupBy('clientes.id', 'clientes.nombres', 'clientes.apellido_paterno', 'clientes.apellido_materno', 'clientes.correo_electronico')
                ->get();

            return response()->json([
                'records' => $cliente,
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
}//class
