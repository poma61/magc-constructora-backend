<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//add
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class DetalleCByContratoRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $rules = [
            //contratos => ningun campo es necesario

            //detalle contrato
            'detalle_contrato.n_de_lote' => 'required',
            'detalle_contrato.n_de_uv' => 'required',
            'detalle_contrato.zona' => 'required',
            'detalle_contrato.terreno_superficie' => 'required|numeric',
            'detalle_contrato.numero_distrito' => 'required|numeric',
            'detalle_contrato.numero_identificacion_terreno' => 'required', //puede string string o numero
            'detalle_contrato.norte_medida_terreno' => 'required|numeric',
            'detalle_contrato.norte_colinda_lote' => 'required',
            'detalle_contrato.sur_medida_terreno' => 'required|numeric',
            'detalle_contrato.sur_colinda_lote' => 'required',
            'detalle_contrato.este_medida_terreno' => 'required|numeric',
            'detalle_contrato.este_colinda_lote' => 'required',
            'detalle_contrato.oeste_medida_terreno' => 'required|numeric',
            'detalle_contrato.oeste_colinda_lote' => 'required',
            'detalle_contrato.construccion_descripcion' => 'required',
            'detalle_contrato.construccion_superficie_terreno' => 'required|numeric',
            'detalle_contrato.construccion_valor_total_literal' => 'required',
            'detalle_contrato.construccion_valor_total_numeral' => 'required|numeric',
            'detalle_contrato.construccion_cantidad_meses_de_entrega' => 'required|numeric',
            'detalle_contrato.construccion_val_couta_inicial_literal' => 'required',
            'detalle_contrato.construccion_val_couta_inicial_numeral' => 'required|numeric',
            'detalle_contrato.construccion_val_couta_mensual_literal' => 'required',
            'detalle_contrato.construccion_val_couta_mensual_numeral' => 'required|numeric',
            'detalle_contrato.construccion_cantidad_couta_mensual' => 'required|numeric',
            'detalle_contrato.primera_val_couta_mensual_numeral' => 'required|numeric',
            'detalle_contrato.segunda_val_couta_mensual_numeral' => 'required|numeric',
            'detalle_contrato.tercera_val_couta_mensual_numeral' => 'required|numeric',
            'detalle_contrato.lugar_firma_contrato' => 'required',
            'detalle_contrato.fecha_firma_contrato' => 'required|date',
        ];

        // Agregar reglas para cada cliente en el array
        foreach ($this->input('clients', []) as $key => $client) {
            //clients=> es el nombre del array de objetos que se envia desde el frontend
            // esta validacion se agrega porque en el frontend se puede enviar un cliente ya registrado anteriormente gracias a la opcion "Cliente registrado"
            //donde se busca al cliente registrado por el CI;
            $rules["clients.{$key}.id"] = 'required'; //cuando se crea un nuevo Cliente en el frontend se en envia el id=0 asi que igual funcionara con nuevos clientes
            $rules["clients.{$key}.nombres"] = 'required';
            $rules["clients.{$key}.apellido_paterno"] = 'required';
            $rules["clients.{$key}.apellido_materno"] = 'required';
            $rules["clients.{$key}.n_de_contacto"] = 'required|numeric';
            $rules["clients.{$key}.ci"] = [
                'required',
                //aplicar la validacion unique cuando el campo status este en true siginifica que el registto no esta eliminado
                //aplicamos el ignore cuando sea un update ya que el ci puede ser el mismo porque es una actualizacion del registro
                //clientes=>es el nombre de la tabla de base de datos
                Rule::unique('clientes', 'ci')->where(function ($query) {
                    $query->where('status', true);
                })->ignore($this->input("clients.{$key}.id")),
            ];
            $rules["clients.{$key}.ci_expedido"] = 'required';
            $rules["clients.{$key}.direccion"] = 'required';

            //empty => devuelve false cuando la variable NO esta vacia y/o null o cuando SI tiene contenido
            if (empty($this->input("clients.{$key}.correo_electronico")) == false) {
                $rules["clients.{$key}.correo_electronico"] = [
                    'email',
                    //aplicar la validacion unique cuando el campo status este en true siginifica que el registto no esta eliminado
                    //aplicamos el ignore cuando sea un update ya que el ci puede ser el mismo porque es una actualizacion del registro
                    Rule::unique('clientes', 'correo_electronico')->where(function ($query) {
                        $query->where('status', true);
                    })->ignore($this->input("clients.{$key}.id")),
                ];
            }
        }



        return $rules;
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
