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
        $rules = [
            'fecha_pago_couta' => 'required|date',
            'monto' => 'required|numeric',
            'id_couta' => 'required|numeric',
            'lugar' => 'required|string',
            'servicio' => 'required|string',
            'metodo_de_pago' => 'required|string',
            'nombres' => 'required|string',
            'apellido_paterno' => 'required|string',
            'apellido_materno' => 'required|string',
            'correo_electronico' => 'required|email',
            'numero_de_contacto' => 'required|numeric',
        ];

        //empty => devuelve false cuando la variable NO esta vacia y/o null o cuando si tiene contenido
        if (empty($this->input('nota')) == false) {
            $rules['nota'] = 'string';
        }

        return $rules;
    }


    protected function failedValidation(Validator $validator): HttpResponseException
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Verificar los campos solicitados!',
                'message_errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}//class
