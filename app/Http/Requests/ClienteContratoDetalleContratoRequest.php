<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//add
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;


class ClienteContratoDetalleContratoRequest extends FormRequest
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
            'detalle_contrato.n_de_lote' => 'required', //puede string string o numero
            'detalle_contrato.n_de_uv' => 'required', //puede string string o numero
            'detalle_contrato.n_de_manzano' => 'required', //puede string string o numero
            'detalle_contrato.zona' => 'required',
            'detalle_contrato.terreno_superficie' => 'required|numeric',
            'detalle_contrato.nombre_urbanizacion' => 'required', //puede string string o numero
            'detalle_contrato.n_identificacion_terreno' => 'required', //puede string string o numero
            'detalle_contrato.norte_medida_terreno' => 'required|numeric',
            'detalle_contrato.norte_colinda_lote' => 'required',
            'detalle_contrato.sur_medida_terreno' => 'required|numeric',
            'detalle_contrato.sur_colinda_lote' => 'required',
            'detalle_contrato.este_medida_terreno' => 'required|numeric',
            'detalle_contrato.este_colinda_lote' => 'required',
            'detalle_contrato.oeste_medida_terreno' => 'required|numeric',
            'detalle_contrato.oeste_colinda_lote' => 'required',
            'detalle_contrato.construccion_descripcion' => 'required',
            'detalle_contrato.construccion_superficie' => 'required|numeric',
            'detalle_contrato.construccion_valor_total_literal' => 'required',
            'detalle_contrato.construccion_valor_total_numeral' => 'required|numeric',
            'detalle_contrato.construccion_cantidad_meses_de_entrega' => 'required|integer',
            'detalle_contrato.construccion_val_couta_inicial_literal' => 'required',
            'detalle_contrato.construccion_val_couta_inicial_numeral' => 'required|numeric',
            'detalle_contrato.construccion_val_couta_mensual_literal' => 'required',
            'detalle_contrato.construccion_val_couta_mensual_numeral' => 'required|numeric',
            'detalle_contrato.fecha_cancelacion_coutas' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $fecha = \Carbon\Carbon::createFromFormat('Y-m-d', $value);
                    // Verificar que el día no sea 28, 30 ni 31
                    if (in_array($fecha->day, [28, 29, 30, 31])) {
                        $fail('Seleccione una fecha válida, evitando los días 28, 29, 30 y 31.');
                    }
                },
            ],
            'detalle_contrato.cantidad_coutas_mensuales' => 'required|integer',
            'detalle_contrato.primera_val_couta_mensual_numeral' => 'required|numeric',
            'detalle_contrato.segunda_val_couta_mensual_numeral' => 'required|numeric',
            'detalle_contrato.tercera_val_couta_mensual_numeral' => 'required|numeric',
            'detalle_contrato.cuarta_val_couta_mensual_numeral' => 'required|numeric',
            'detalle_contrato.lugar_firma_contrato' => 'required',
            'detalle_contrato.fecha_firma_contrato' => 'required|date',
        ];
        if ($this->input('detalle_contrato.add_info_terreno')) {
            $rules['detalle_contrato.terreno_valor_total_numeral'] = 'required|numeric';
            $rules['detalle_contrato.terreno_valor_total_literal'] = 'required|string';
            $rules['detalle_contrato.terreno_val_couta_inicial_numeral'] = 'required|numeric';
            $rules['detalle_contrato.terreno_val_couta_mensual_numeral'] = 'required|numeric';
        }
        //SOLO VALIDAMOS CUANDO ES UN NUEVO REGISTRO POR EL METODO POST
        //PORQUE AL EDITAR EL REGISTRO NO EDITAMOS LOS DATOS DEL CLIENTE
        if ($this->isMethod('POST')) {
            switch ($this->input('type_of_register_client')) {
                case 'cliente-nuevo':
                    // Agregar reglas para cada cliente en el array
                    foreach ($this->input('clients', []) as $key => $client) {
                        //verificar que ningun campo
                        //clients=> es el nombre del array de objetos que se envia desde el frontend
                        // esta validacion se agrega porque en el frontend se puede enviar un cliente ya registrado anteriormente gracias a la opcion "Cliente registrado"
                        //donde se busca al cliente registrado por el CI;
                        $rules["clients.{$key}.nombres"] = 'required';
                        $rules["clients.{$key}.apellido_paterno"] = 'required';
                        $rules["clients.{$key}.apellido_materno"] = 'required';
                        $rules["clients.{$key}.n_de_contacto"] = 'required|integer';
                        $rules["clients.{$key}.ci"] = [
                            'required',
                            //aplicar la validacion unique cuando el campo status este en true siginifica que el registto no esta eliminado
                            //aplicamos el ignore cuando sea un update ya que el ci puede ser el mismo porque es una actualizacion del registro
                            //clientes=>es el nombre de la tabla de base de datos
                            Rule::unique('clientes', 'ci')->where(function ($query) {
                                $query->where('status', true);
                            })->ignore($this->input("clients.{$key}.id")),
                        ];
                        $rules["clients.*.ci"] = "distinct";//verifica si el usuario envia ci duplicados en el json
                        $rules["clients.{$key}.ci_expedido"] = 'required';
                        $rules["clients.{$key}.direccion"] = 'required';
                    }
                    break;
                case 'cliente-registrado':
                    // Agregar reglas para cada cliente en el array
                    foreach ($this->input('clients', []) as $key => $client) {
                        //clients=> es el nombre del array de objetos que se envia desde el frontend
                        // esta validacion se agrega porque en el frontend se puede enviar un cliente ya registrado anteriormente gracias a la opcion "Cliente registrado"
                        //donde se busca al cliente registrado por el CI;
                        $rules["clients.{$key}.id"] = 'required';
                        $rules["clients.{$key}.nombres"] = 'required';
                        $rules["clients.{$key}.apellido_paterno"] = 'required';
                        $rules["clients.{$key}.apellido_materno"] = 'required';
                        $rules["clients.{$key}.ci"] = 'required';
                    }
                    break;
                default:
                    $rules['type_of_register_client'] = 'required';
                    break;
            }
        } //if

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            //detalle contrato
            'detalle_contrato.n_de_lote.required' => 'El campo n° de lote es requerido.',
            'detalle_contrato.n_de_uv.required' => 'El campo n° de uv es requerido.',
            'detalle_contrato.n_de_manzano.required' => 'El campo n° de manzano es requerido.',
            'detalle_contrato.zona.required' => 'El campo zona es requerido.',
            'detalle_contrato.terreno_superficie.required' => 'El campo superficie de terreno es requerido.',
            'detalle_contrato.terreno_superficie.numeric' => 'El campo superficie de terreno debe ser un número.',
            'detalle_contrato.nombre_urbanizacion.required' => 'El campo nombre de urbanizacion es requerido.',
            'detalle_contrato.n_identificacion_terreno.required' => 'El campo n° de identificación de terreno es requerido.',
            'detalle_contrato.norte_medida_terreno.required' => 'El campo norte, medida del terreno es requerido.',
            'detalle_contrato.norte_medida_terreno.numeric' => 'El campo norte, medida del terreno debe ser un número.',
            'detalle_contrato.norte_colinda_lote.required' => 'El campo norte, colinda con lote es requerido.',
            'detalle_contrato.sur_medida_terreno.required' => 'El campo sur, medida del terreno es requerido.',
            'detalle_contrato.sur_medida_terreno.numeric' => 'El campo sur, medida del terreno debe ser un número.',
            'detalle_contrato.sur_colinda_lote.required' => 'El campo sur, colinda con lote es requerido.',
            'detalle_contrato.este_medida_terreno.required' => 'El campo este, medida del terreno es requerido.',
            'detalle_contrato.este_medida_terreno.numeric' => 'El campo este, medida del terreno debe ser un número.',
            'detalle_contrato.este_colinda_lote.required' => 'El campo este, colinda con lote es requerido.',
            'detalle_contrato.oeste_medida_terreno.required' => 'El campo oeste, medida del terreno es requerido.',
            'detalle_contrato.oeste_medida_terreno.numeric' => 'El campo oeste, medida del terreno debe ser un número.',
            'detalle_contrato.oeste_colinda_lote.required' => 'El campo oeste, colinda con lote es requerido.',
            'detalle_contrato.construccion_descripcion.required' => 'El campo descripción del inmueble, construcción es requerido.',
            'detalle_contrato.construccion_superficie.required' => 'El campo superficie del terreno, construcción es requerido.',
            'detalle_contrato.construccion_superficie.numeric' => 'El campo superficie del terreno, construcción debe ser un número.',
            'detalle_contrato.construccion_valor_total_literal.required' => 'El campo valor total en literal de la construcción es requerido.',
            'detalle_contrato.construccion_valor_total_numeral.required' => 'El campo valor total de la construcción es requerido.',
            'detalle_contrato.construccion_valor_total_numeral.numeric' => 'El campo valor total de la construcción debe ser un número.',
            'detalle_contrato.construccion_cantidad_meses_de_entrega.required' => 'El campo cantidad de meses de entrega, construcción es requerido.',
            'detalle_contrato.construccion_cantidad_meses_de_entrega.integer' => 'El campo detalle cantidad de meses de entrega, construcción debe ser un entero.',
            'detalle_contrato.construccion_val_couta_inicial_literal.required' => 'El campo couta inicial en literal de la construcción es requerido.',
            'detalle_contrato.construccion_val_couta_inicial_numeral.required' => 'El campo couta inicial de la construcción es requerido.',
            'detalle_contrato.construccion_val_couta_inicial_numeral.numeric' => 'El campo couta inicial de la construcción debe ser un número.',
            'detalle_contrato.construccion_val_couta_mensual_literal.required' => 'El campo couta mensual en literal de la construcción es requerido.',
            'detalle_contrato.construccion_val_couta_mensual_numeral.required' => 'El campo couta mensual en numeral de la construcción es requerido.',
            'detalle_contrato.construccion_val_couta_mensual_numeral.numeric' => 'El campo  couta mensual de la construcción debe ser un número.',
            'detalle_contrato.fecha_cancelacion_coutas.required' => 'El campo fecha de cancelacion de coutas es requerido.',
            'detalle_contrato.fecha_cancelacion_coutas.date' => 'El campo fecha de cancelacion de coutas no es una fecha válida.',
            'detalle_contrato.fecha_cancelacion_coutas.after_or_equal' => 'El campo fecha de cancelacion de coutas debe ser una fecha después o igual a :date.',
            'detalle_contrato.fecha_cancelacion_coutas.before_or_equal' => 'El campo fecha de cancelacion de coutas debe ser una fecha antes o igual a :date.',
            'detalle_contrato.cantidad_coutas_mensuales.required' => 'El campo cantidad de coutas mensuales es requerido.',
            'detalle_contrato.cantidad_coutas_mensuales.integer' => 'El campo cantidad de coutas mensuales debe ser un entero.',
            'detalle_contrato.primera_val_couta_mensual_numeral.required' => 'El campo primera couta mensual es requerido.',
            'detalle_contrato.primera_val_couta_mensual_numeral.numeric' => 'El campo primera couta mensual debe ser un número.',
            'detalle_contrato.segunda_val_couta_mensual_numeral.required' => 'El campo segunda couta mensual es requerido.',
            'detalle_contrato.segunda_val_couta_mensual_numeral.numeric' => 'El campo segunda couta mensual debe ser un número.',
            'detalle_contrato.tercera_val_couta_mensual_numeral.required' => 'El campo tercera couta mensual es requerido.',
            'detalle_contrato.tercera_val_couta_mensual_numeral.numeric' => 'El campo tercera couta mensual debe ser un número.',
            'detalle_contrato.cuarta_val_couta_mensual_numeral.required' => 'El campo cuarta couta mensual es requerido.',
            'detalle_contrato.cuarta_val_couta_mensual_numeral.numeric' => 'El campo cuarta couta mensual debe ser un número.',
            'detalle_contrato.lugar_firma_contrato.required' => 'El campo lugar de la firma del contrato es requerido.',
            'detalle_contrato.fecha_firma_contrato.required' => 'El campo fecha de la firma del contrato es requerido.',
            'detalle_contrato.fecha_firma_contrato.date' => 'El campo fecha de la firma del contrato no es una fecha válida',
            //add info terreno
            'detalle_contrato.terreno_valor_total_numeral.required' => 'El campo valor total del terreno es requerido.',
            'detalle_contrato.terreno_valor_total_numeral.numeric' => 'El campo valor total del terreno debe ser un número.',
            'detalle_contrato.terreno_valor_total_literal.required' => 'El campo valor total en literal del terreno en es requerido.',
            'detalle_contrato.terreno_val_couta_inicial_numeral.required' => 'El campo couta inicial del terreno es requerido.',
            'detalle_contrato.terreno_val_couta_inicial_numeral.numeric' => 'El campo couta inicial del terreno debe ser un número.',
            'detalle_contrato.terreno_val_couta_mensual_numeral.required' => 'El campo couta mensual del terreno es requerido.',
            'detalle_contrato.terreno_val_couta_mensual_numeral.numeric' => 'El campo couta mensual del terreno debe ser un número.',
            //otros posibles errores
            'type_of_register_client.required' => 'El campo type_of_register_client no tiene un modo de registro del cliente.'

        ];

        foreach ($this->input('clients', []) as $key => $client) {
            //clients=> es el nombre del array de objetos que se envia desde el frontend
            $messages["clients.{$key}.id.required"] = 'El campo id es requerido.';
            $messages["clients.{$key}.nombres.required"] = 'El campo nombres es requerido.';
            $messages["clients.{$key}.apellido_paterno.required"] = 'El campo apellido paterno es requerido.';
            $messages["clients.{$key}.apellido_materno.required"] = 'El campo apellido materno es requerido.';
            $messages["clients.{$key}.n_de_contacto.required"] = 'El campo n° de contacto es requerido.';
            $messages["clients.{$key}.n_de_contacto.integer"] = 'El campo n° de contacto debe ser un número entero.';
            $messages["clients.{$key}.ci.required"] = 'El campo ci es requerido.';
            $messages["clients.{$key}.ci.unique"] = 'El campo ci ya ha sido tomado.';
            $messages["clients.*.ci.distinct"] = 'El campo ci tiene un valor duplicado.';
            $messages["clients.{$key}.ci_expedido.required"] = 'El campo expedido es requerido.';
            $messages["clients.{$key}.direccion.required"] = 'El campo direccion es requerido.';
            $messages["clients.{$key}.correo_electronico.email"] = 'El formato del correo electronico no es válido.';
            $messages["clients.{$key}.correo_electronico.unique"] = 'El campo correo electronico ya ha sido tomado.';
        }

        return $messages;
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
