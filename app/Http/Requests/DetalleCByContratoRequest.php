<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//add
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class DetalleCByContratoRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            //clientes_has_contratos
            'id_cliente' => 'required',
            //contratos
            'descripcion' => 'required',

            //detalle contrato
            'n_de_lote' => 'required',
            'n_de_uv' => 'required',
            'zona' => 'required',
            'superficie_terreno' => 'required|numeric',
            'numero_distrito' => 'required',
            'numero_identificacion_terreno' => 'required',
            'norte_medida_terreno' => 'required|numeric',
            'norte_colinda_lote' => 'required',
            'sur_medida_terreno' => 'required|numeric',
            'sur_colinda_lote' => 'required',
            'este_medida_terreno' => 'required|numeric',
            'este_colinda_lote' => 'required',
            'oeste_medida_terreno' => 'required|numeric',
            'oeste_colinda_lote' => 'required',
            'valor_construccion_literal' => 'required',
            'valor_construccion_numeral' => 'required|numeric',
            'valor_couta_inicial_literal' => 'required',
            'valor_couta_inicial_numeral' => 'required|numeric',
            'valor_couta_mensual_literal' => 'required',
            'valor_couta_mensual_numeral' => 'required|numeric',
            'primera_val_couta_mensual_numeral' => 'required|numeric',
            'segunda_val_couta_mensual_numeral' => 'required|numeric',
            'tercera_val_couta_mensual_numeral' => 'required|numeric',
            'lugar_firma_contrato' => 'required',
            'fecha_firma_contrato' => 'required|date',
        ];
    }

    protected function failedValidation(Validator $validator): HttpResponseException
    {
        $response = [
            'status' => false,
            'message' => 'Verificar los campos solicitados!',
            'message_errors' => $validator->errors(),
        ];

        throw new HttpResponseException(response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}//class
