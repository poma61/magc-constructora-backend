<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//add
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Contracts\Validation\Validator;


class TransaccionPagoCoutaRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Verificar si no se proporcionaron transacciones
        //empty => devuelve TRUE cuando la variable SI esta vacia y/o es NULL
        if (empty($this->input('transacciones'))) {
            return [
                'transacciones' => 'required'
            ];
        }

        //$this->input('transacciones', [])=> si no hay ninguna array entonces coloca un array vacio
        //para evitar errores
        foreach ($this->input('transacciones', []) as $key => $transaction) {
            $rules["transacciones.{$key}.fecha_pago_couta"] = 'required|date';
            $rules["transacciones.{$key}.monto"] = 'required|numeric';
            $rules["transacciones.{$key}.moneda"] = 'required|string';
            $rules["transacciones.{$key}.lugar"] = 'required|string';
            $rules["transacciones.{$key}.servicio"] = 'required|string';
            $rules["transacciones.{$key}.metodo_de_pago"] = 'required|string';
            $rules["transacciones.{$key}.nombres"] = 'required|string';
            $rules["transacciones.{$key}.apellidos"] = 'required|string';
            $rules["transacciones.{$key}.correo_electronico"] = 'required|email';
            $rules["transacciones.{$key}.numero_de_contacto"] = 'required|numeric|integer';
            $rules["transacciones.{$key}.id_couta"] = 'required|numeric|integer' ;
            $rules["transacciones.*.id_couta"] = 'distinct' ;

            //empty => devuelve false cuando la variable NO esta vacia y/o null o cuando si tiene contenido
            if (empty($this->input("transacciones.{$key}.nota")) == false) {
                $rules["transacciones.{$key}.nota"] = 'string';
            }
        }
        return $rules;
    }//rules

    public function messages(): array
    {
        $messages=[];
        foreach ($this->input('transacciones', []) as $key => $transaction) {
            $messages["transacciones.{$key}.fecha_pago_couta.required"] = 'El campo fecha pago couta es requerido.';
            $messages["transacciones.{$key}.fecha_pago_couta.date"] = 'El campo fecha pago couta no es una fecha válida.';

            $messages["transacciones.{$key}.monto.required"] = 'El campo monto es requerido.';
            $messages["transacciones.{$key}.monto.numeric"] = 'El campo monto debe ser un número.';

            $messages["transacciones.{$key}.moneda.required"] = 'El campo moneda es requerido.';
            $messages["transacciones.{$key}.moneda.string"] = 'El campo moneda debe ser una cadena.';

            $messages["transacciones.{$key}.lugar.required"] = 'El campo lugar es requerido.';
            $messages["transacciones.{$key}.lugar.string"] = 'El campo lugar debe ser una cadena.';

            $messages["transacciones.{$key}.servicio.required"] = 'El campo servicio es requerido.';
            $messages["transacciones.{$key}.servicio.string"] = 'El campo servicio debe ser una cadena.';

            $messages["transacciones.{$key}.metodo_de_pago.required"] = 'El campo metodo de pago es requerido.';
            $messages["transacciones.{$key}.metodo_de_pago.string"] = 'El campo metodo de pago debe ser una cadena.';

            $messages["transacciones.{$key}.nombres.required"] = 'El campo nombres es requerido.';
            $messages["transacciones.{$key}.nombres.string"] ='El campo nombres debe ser una cadena.';

            $messages["transacciones.{$key}.apellidos.required"] = 'El campo apellidos es requerido.';
            $messages["transacciones.{$key}.apellidos.string"] = 'El campo apellidos debe ser una cadena.';

            $messages["transacciones.{$key}.correo_electronico.required"] = 'El campo correo electronico es requerido.';
            $messages["transacciones.{$key}.correo_electronico.email"] = 'El formato del correo electronico no es válido.';

            $messages["transacciones.{$key}.numero_de_contacto.required"] = 'El campo numero de contacto es requerido.';
            $messages["transacciones.{$key}.numero_de_contacto.numeric"] = 'El campo numero de contacto debe ser un número.';
            $messages["transacciones.{$key}.numero_de_contacto.integer"] = 'El campo numero de contacto debe ser un entero.';

            $messages["transacciones.{$key}.id_couta.required"] = 'El campo id couta requerido.';
            $messages["transacciones.{$key}.id_couta.numeric"] = 'El campo id couta debe ser un número.';
            $messages["transacciones.{$key}.id_couta.integer"] = 'El campo id couta debe ser un entero.';
            $messages["transacciones.*.id_couta.distinct"] ='El campo id couta tiene un valor duplicado.';

            $messages["transacciones.{$key}.nota.string"] ='El campo nota debe ser una cadena.';
        }
        return $messages;

    }

    protected function failedValidation(Validator $validator): HttpResponseException
    {
        //Verificar si no se proporcionaron transacciones
        //empty => devuelve TRUE cuando la variable SI esta vacia y/o null
        if (empty($this->input('transacciones'))) {
            throw new HttpResponseException(
                response()->json([
                    'status' => false,
                    'message' => 'No se han proporcionado transacciones y/o no se pueden procesar los datos!',
                    'message_errors' => []
                ], Response::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Verificar los campos solicitados!',
                'message_errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}//class
