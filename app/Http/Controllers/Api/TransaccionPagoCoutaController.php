<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//add
use App\Models\Cliente;
use App\Models\Desarrolladora;
use App\Models\TransaccionPagoCouta;
use Throwable;


class TransaccionPagoCoutaController extends Controller
{
    public function indexListCliente(Request $request)
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
                ->join('clientes_has_contratos', 'clientes_has_contratos.id_cliente', '=', 'clientes.id')
                ->join('contratos', 'contratos.id', '=', 'clientes_has_contratos.id_contrato')
                ->select(
                    'clientes.nombres',
                    'clientes.apellido_paterno',
                    'clientes.apellido_materno',
                    'contratos.n_contrato',
                )
                ->where('clientes.status', true)
                ->where('contratos.status', true)
                ->where('desarrolladoras.id', $desarrolladora->id)
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

    public function indexListTransaction(Request $request)
    {
        try {
            if ($request->input('transaction_status')) {
                $transaccion = TransaccionPagoCouta::join('coutas', 'coutas.id', '=', 'transacciones_pago_coutas.id_couta')
                    ->join('contratos', 'contratos.id', '=', 'coutas.id_contrato')
                    ->select(
                        'coutas.id',
                        'coutas.num_couta',
                        'transacciones_pago_coutas.*'
                    )
                    ->where('contratos.status', true)
                    ->where('coutas.status', true)
                    ->where('transacciones_pago_coutas.transaction_status', true)
                    ->where('contratos.n_contrato', $request->input('n_contrato'))
                    ->get();
            } else {
                $transaccion = TransaccionPagoCouta::join('coutas', 'coutas.id', '=', 'transacciones_pago_coutas.id_couta')
                    ->join('contratos', 'contratos.id', '=', 'coutas.id_contrato')
                    ->select(
                        'coutas.id',
                        'coutas.num_couta',
                        'transacciones_pago_coutas.*'
                    )
                    ->where('contratos.status', true)
                    ->where('coutas.status', true)
                    ->where('transacciones_pago_coutas.transaction_status', false)
                    ->where('contratos.n_contrato', $request->input('n_contrato'))
                    ->get();
            }

            return response()->json([
                'records' => $transaccion,
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
