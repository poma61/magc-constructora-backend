<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransaccionPagoCoutaRequest;
use App\Models\Cliente;
use App\Models\Couta;
use App\Models\TransaccionPagoCouta;
use Illuminate\Http\Request;
use Throwable;

class MultipagoController extends Controller
{

    public function store(TransaccionPagoCoutaRequest $request)
    {
        try {
            $transaccion = TransaccionPagoCouta::where('id_couta', $request->input('id_couta'))
                ->where('transaction_status', true)
                ->first();

            //en la base de datos solo debe de haber una transaction_status=true para cada couta
            //por eso por estabilidad de los datos se verifica si hay trasancciones vigentes,
            //puede hacer multiples transacciones anuladas, pero solo puede haber una transaccion vigente para cada couta
            //la vigencia de la trasanccion se define por transaction_status  true=>vigente, false=> anulada
            if ($transaccion != null) {
                return response()->json([
                    'result' => $transaccion,
                    'status' => false,
                    'message' => "Hay una transacción vigente, debe anular la transacción para crear una nueva transacción."
                ], 422);
            }

            $transaccion = new TransaccionPagoCouta($request->all());
            $transaccion->transaction_status = true;
            $transaccion->save();

            return response()->json([
                'result' => $transaccion,
                'status' => true,
                'message' => "OK"
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'result' => null,
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    } //store


    public function update(TransaccionPagoCoutaRequest $request)
    {
        try {
            $transaccion = TransaccionPagoCouta::where('id', $request->input('id'))
                ->where('transaction_status', true)
                ->first();

            if ($transaccion == null) {
                return response()->json([
                    'result' => null,
                    'status' => false,
                    'message' => "Esta transacción fue actualizada.",
                ], 404);
            }

            $transaccion->update($request->all());

            return response()->json([
                'result' => $transaccion,
                'status' => true,
                'message' => "OK"
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'result' => null,
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    } //update

    public function invalidateTransaction(Request $request)
    {
        try {
            $transaccion = TransaccionPagoCouta::where('id', $request->input('id'))
                ->where('transaction_status', true)
                ->first();

            if ($transaccion == null) {
                return response()->json([
                    'status' => false,
                    'message' => "Esta transacción no se encuentra en el sistema!",
                ], 404);
            }
            $transaccion->transaction_status = false;
            $transaccion->update();

            return response()->json([
                'status' => true,
                'message' => "La transacción fue anulada!"
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function recordContratoByCi(Request $request)
    {
        try {
            $contrato = Cliente::join('clientes_has_contratos', 'clientes_has_contratos.id_cliente', '=', 'clientes.id')
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

            if ($contrato->count() > 0) {
                return response()->json([
                    'contratos' => $contrato,
                    'status' => true,
                    'message' => "Cliente con Carnet de Identidad numero {$request->input('ci')}.",
                ], 200);
            } else {
                return response()->json([
                    'contratos' => null,
                    'status' => false,
                    'message' => "No se encontro el cliente con Carnet de Identidad numero {$request->input('ci')}"
                ], 404);
            }
        } catch (Throwable $th) {
            return response()->json([
                'contratos' => null,
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function recordCoutasByNumContrato(Request $request)
    {
        try {
            $coutas = Couta::join('contratos', 'contratos.id', '=', 'coutas.id_contrato')
                ->leftJoin('transacciones_pago_coutas', 'transacciones_pago_coutas.id_couta', '=', 'coutas.id')
                ->select(
                    'coutas.id as id_couta',
                    'coutas.num_couta',
                    'coutas.fecha_maximo_pago_couta',
                    'coutas.monto',
                    'contratos.n_contrato',
                    'transacciones_pago_coutas.transaction_status as couta_pagada'
                )
                ->where('contratos.status', true)
                ->where('coutas.status', true)
                ->whereRaw(
                    "NOT EXISTS (
                            SELECT 1
                            FROM transacciones_pago_coutas AS sub_transacciones_pago_coutas
                            WHERE sub_transacciones_pago_coutas.id_couta = coutas.id
                            AND sub_transacciones_pago_coutas.transaction_status <> 0
                        )"
                ) //0 =>  false
                ->where('contratos.n_contrato', $request->input('n_contrato'))
                ->orderBy('coutas.num_couta', 'ASC')
                //se asegura de mostrar datos unicos , ya que al hacer un join con transacciones_pago_coutas
                //podria mostrar la misma couta dos veces, es decir, en caso de que en transacciones_pago_coutas haya mas de un registro perteneciente
                // a un registro de la tabla coutas entonces la couta se mostrara mas de una vez segun la cantidad de registros de transacciones_pago_coutas
                //porque estamos haciendo un join, entonces para evitar eso se hace el distinct
                ->distinct()
                ->get();
            // el codigo es euivalente a 
            // SELECT DISTINCT
            //     coutas.id,
            //     coutas.num_couta,
            //     coutas.fecha_maximo_pago_couta,
            //     coutas.monto,
            //     contratos.n_contrato,
            //     transacciones_pago_coutas.transaction_status
            // FROM
            //     coutas
            // JOIN
            //     contratos ON contratos.id = coutas.id_contrato
            // LEFT JOIN
            //     transacciones_pago_coutas ON transacciones_pago_coutas.id_couta = coutas.id
            // WHERE
            //     contratos.status = 1
            //     AND coutas.status = 1
            //     AND NOT EXISTS (
            //         SELECT 1
            //         FROM transacciones_pago_coutas AS sub_transacciones_pago_coutas
            //         WHERE sub_transacciones_pago_coutas.id_couta = coutas.id
            //         AND sub_transacciones_pago_coutas.transaction_status <> 0
            //     )
            //     AND contratos.n_contrato = :n_contrato
            // ORDER BY
            //     coutas.num_couta ASC;

            //NOT EXISTS devuelve verdadero (TRUE) si la subconsulta no devuelve ninguna fila, es decir, 
            //si no hay ninguna fila que cumpla con las condiciones especificadas en la subconsulta.
            //Si la subconsulta devuelve al menos una fila (en este caso, 1), NOT EXISTS devuelve falso (FALSE).

            //NOT EXISTS se utiliza en combinación con la cláusula WHERE para filtrar las filas en la consulta principal.
            // Si la subconsulta en el NOT EXISTS devuelve true (es decir, no encuentra registros que cumplan con las condiciones), 
            //entonces la fila en la tabla principal (en este caso, la tabla coutas) será incluida en los resultados de la consulta principal.
            //ENTONCES
            //0 <> 0 => FALSE
            //1 <> 0 => TRUE

            //SELECT *:
            //Recupera todas las columnas de la tabla.
            //Puede tener un impacto en el rendimiento si hay muchas columnas o si estás seleccionando más datos de los que realmente necesitas.
            //SELECT 1:
            //Devuelve un valor constante (1) para cada fila que cumple con las condiciones especificadas.
            //Es más eficiente en términos de rendimiento, ya que solo necesita comprobar la existencia de
            // al menos un registro, sin recuperar ni procesar todas las columnas de la tabla.

            if ($coutas->count() > 0) {
                return response()->json([
                    'coutas' => $coutas,
                    'status' => true,
                    'message' => "OK",
                ], 200);
            } else {
                return response()->json([
                    'coutas' => null,
                    'status' => false,
                    'message' => "No se encontro registros de coutas que pertenezca al contrato {$request->input('n_contrato')}.",
                ], 404);
            }
        } catch (Throwable $th) {
            return response()->json([
                'coutas' => null,
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}//class
