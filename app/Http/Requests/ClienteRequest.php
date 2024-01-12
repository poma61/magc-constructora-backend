<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//add
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;


class ClienteRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $rules = [
            'nombres' => 'required',
            'apellido_paterno' => 'required',
            'apellido_materno' => 'required',
            'n_de_contacto' => 'required|numeric',
            'ci' => [
                'required',
                //aplicar la validacion unique cuando el campo status este en true siginifica que el registto no esta eliminado
                //aplicamos el ignore cuando sea un update ya que el ci puede ser el mismo porque es una actualizacion del registro
                Rule::unique('clientes')->where(function ($query) {
                    $query->where('status', true);
                })->ignore($this->input('id')),
            ],
            'ci_expedido' => 'required',
            'direccion' => 'required',
        ];

        //empty => devuelve false cuando la variable NO esta vacia y/o null o cuando SI tiene contenido
        if (empty($this->input('correo_electronico')) == false) {
            $rules['correo_electronico'] = [
                'email',
                //aplicar la validacion unique cuando el campo status este en true siginifica que el registto no esta eliminado
                //aplicamos el ignore cuando sea un update ya que el ci puede ser el mismo porque es una actualizacion del registro
                Rule::unique('clientes')->where(function ($query) {
                    $query->where('status', true);
                })->ignore($this->input('id')),
            ];
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
