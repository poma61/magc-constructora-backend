<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//add
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;


class DesarrolladoraRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $rules = [
            'nombres' => 'required',
            'direccion' => 'required',
        ];

        //empty => devuelve false cuando la variable NO esta vacia y/o null o cuando SI tiene contenido
        if (empty($this->input('correo_electronico')) == false) {
            $rules['correo_electronico'] = [
                'email',
                //aplicar la validacion unique cuando el campo status este en true siginifica que el registto no esta eliminado
                //aplicamos el ignore cuando sea un update ya que el ci puede ser el mismo porque es una actualizacion del registro
                Rule::unique('desarrolladoras')->where(function ($query) {
                    $query->where('status', true);
                })->ignore($this->input('id')),
            ];
        }

        if ($this->isMethod('PUT')) {
            $rules['logo'] = 'sometimes|mimes:jpeg,png,jpg';
        } else {
            $rules['logo'] = 'required|mimes:jpeg,png,jpg';
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
