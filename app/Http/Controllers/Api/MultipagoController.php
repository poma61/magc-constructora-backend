<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Couta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                ->leftJoin('historial_de_pago_coutas', 'historial_de_pago_coutas.id_couta', '=', 'coutas.id')
                ->select(
                    'coutas.id',
                    'coutas.num_couta',
                    'coutas.fecha_maximo_pago_couta',
                    'coutas.monto',
                    'contratos.n_contrato',
                    'historial_de_pago_coutas.pago_valido as couta_pagada'
                )
                ->where('contratos.status', true)
                ->where('coutas.status', true)
                ->whereRaw(
                    "NOT EXISTS (
                            SELECT 1
                            FROM historial_de_pago_coutas AS sub_historial
                            WHERE sub_historial.id_couta = coutas.id
                            AND sub_historial.pago_valido <> 0
                        )"
                ) //0 =>  false
                ->where('contratos.n_contrato', $request->input('n_contrato'))
                ->orderBy('coutas.num_couta', 'ASC')
                //se asegura de mostrar datos unicos , ya que al hacer un join con historial_de_pago_coutas
                //podria mostrar la misma couta dos veces, es decir, en caso de que en historial_de_pago_coutas haya mas de un registro perteneciente
                // a un registro de la tabla coutas entonces la couta se mostrara mas de una vez segun la cantidad de registros de historial_de_pago_coutas
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
            //     historial_de_pago_coutas.pago_valido
            // FROM
            //     coutas
            // JOIN
            //     contratos ON contratos.id = coutas.id_contrato
            // LEFT JOIN
            //     historial_de_pago_coutas ON historial_de_pago_coutas.id_couta = coutas.id
            // WHERE
            //     contratos.status = 1
            //     AND coutas.status = 1
            //     AND NOT EXISTS (
            //         SELECT 1
            //         FROM historial_de_pago_coutas AS sub_historial
            //         WHERE sub_historial.id_couta = coutas.id
            //         AND sub_historial.pago_valido <> 0
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
