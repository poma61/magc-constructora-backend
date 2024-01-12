<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//add
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class PersonalRequest extends FormRequest
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
            'cargo' => 'required',
            'ci' => [
                'required',
                //aplicar la validacion unique cuando el campo status este en true siginifica que el registto no esta eliminado
                //aplicamos el ignore cuando sea un update ya que el ci puede ser el mismo porque es una actualizacion del registro
                Rule::unique('personals')->where(function ($query) {
                    $query->where('status', true);
                })->ignore($this->input('id')),
            ],
            'ci_expedido' => 'required',
        ];
        if ($this->isMethod('PUT')) {
            $rules['foto'] = 'sometimes|mimes:jpeg,png,jpg';
        } else {
            $rules['foto'] = 'required|mimes:jpeg,png,jpg';
        }

        //empty => devuelve false cuando la variable NO esta vacia y/o null o cuando SI tiene contenido
        if (empty($this->input('correo_electronico')) == false) {
            $rules['correo_electronico'] = [
                'email',
                //aplicar la validacion unique cuando el campo status este en true siginifica que el registto no esta eliminado
                //aplicamos el ignore cuando sea un update ya que el ci puede ser el mismo porque es una actualizacion del registro
                Rule::unique('personals')->where(function ($query) {
                    $query->where('status', true);
                })->ignore($this->input('id')),
            ];
        }

        //empty => devuelve false cuando la variable NO esta vacia y/o null o cuando SI tiene contenido
        if (empty($this->input('n_de_contacto')) == false) {
            $rules['n_de_contacto'] = 'numeric';
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
        throw  new HttpResponseException(response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}//class
