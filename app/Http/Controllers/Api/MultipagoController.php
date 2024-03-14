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

    public function storeTransaction(TransaccionPagoCoutaRequest $request)
    {
        try {
            foreach ($request->input("transacciones", []) as $transaccion_values) {
                //verificamos si la couta existe para evitar errores con llaves foraneas
                $couta = Couta::where('id', $transaccion_values['id_couta'])
                    ->where('status', true)
                    ->exists();// el metodo exists() devuelve "true" cuando encuentra al menos un registro y false cuando no hay registro(s)
                if (!$couta) {
                    return response()->json([
                        'result' => null,
                        'status' => false,
                        'message' => "La couta con id {$transaccion_values['id_couta']} no se encuentra en el sistema!"
                    ], 404);
                }

                $transaccion = TransaccionPagoCouta::where('id_couta', $transaccion_values['id_couta'])
                    ->where('transaction_status', true)
                    ->first();

                //en la base de datos solo debe de haber una transaction_status=true para cada couta
                //por estabilidad de los datos se verifica si hay trasancciones vigentes,
                //puede haber multiples transacciones anuladas, pero solo puede haber una transaccion vigente para cada couta
                //la vigencia de la trasanccion se define por transaction_status  true=>vigente, false=> anulada
                if ($transaccion != null) {
                    //creamos el 'id_transaccion' para que el desarrollador frontend pueda entender mejor los datos
                    $transaccion->id_transaccion = $transaccion->id;
                    unset($transaccion->id);//eliminamos $transaccion->id

                    return response()->json([
                        'result' => $transaccion,
                        'status' => false,
                        'message' => "Hay una transacción vigente, debe anular la transacción para crear una nueva transacción."
                    ], 422);
                }
            }

            $__transacciones = $request->input("transacciones", []);
            foreach ($__transacciones as $transaccion_values) {
                $transaccion = new TransaccionPagoCouta($transaccion_values);
                $transaccion->transaction_status = true;
                $transaccion->save();

                //creamos el 'id_transaccion' para que el desarrollador frontend pueda entender mejor los datos
                $transaccion->id_transaccion = $transaccion->id;
                unset($transaccion->id);//eliminamos $transaccion->id

                $registered_transacciones[] = $transaccion;
            }

            return response()->json([
                'results' => $registered_transacciones,
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
    } //storeTransaction


    public function invalidateTransaction(Request $request)
    {
        try {

            foreach ($request->input("transacciones") as $transaccion_values) {

                //verificamos si la transaccion a anular existe
                //Hacemos esto porque hay la posibilidad de que el id_couta este eliminada y/o talvez este id_couta ya fue cancelada anteriormente
                //La couta se elimina cuando actualizamos el contrato entonces la cantidad de coutas se vuelven a generar
                //para resguardar integridad de los datos entonces por eso  verificamos si dicha couta esta vigente en la base de datos
                //En otros casos este paso no es necesario, pero como estamos registrando Transacciones de pagos entonces ahi si es necesario

                $couta = Couta::join('transacciones_pago_coutas', 'transacciones_pago_coutas.id_couta', '=', 'coutas.id')
                    ->where('transacciones_pago_coutas.id', $transaccion_values['id_transaccion'])
                    ->where('coutas.status', true)
                    ->where('transacciones_pago_coutas.transaction_status', true)
                    ->exists(); // el metodo exists() devuelve "true" cuando encuentra al menos un registro y false cuando no hay registro(s)
          
                if (!$couta) {
                    return response()->json([
                        'status' => false,
                        'message' => "Esta transacción no se encuentra en el sistema y/o ya fue anulada!"
                    ], 404);
                }

                $transaccion = TransaccionPagoCouta::where('id', $transaccion_values['id_transaccion'])
                    ->first();

                $transaccion->transaction_status = false;
                $transaccion->update();
            }

            return response()->json([
                'status' => true,
                'message' => "Transaccion(es) anulada(s)!"
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function recordContratoByCiCliente(Request $request)
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
                    'message' => "No se encontro el cliente con Carnet de Identidad numero {$request->input('ci')} y/o el cliente no tiene contratos!"
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
            //Se debe hacer un join con  transacciones_pago_coutas para verificar si hay coutas pagadas
            //solo de es forma se puede saber si la couta esta pagada o no
            $coutas = Couta::join('contratos', 'contratos.id', '=', 'coutas.id_contrato')
                ->leftJoin('transacciones_pago_coutas', 'transacciones_pago_coutas.id_couta', '=', 'coutas.id')
                ->select(
                    'coutas.id as id_couta',
                    'coutas.num_couta',
                    'coutas.fecha_maximo_pago_couta',
                    'coutas.monto',
                    'coutas.moneda',
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
