<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//add
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;


class UsuarioByRoleRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            // si estamos haciendo un update debe permitir el ingreso del mismo usuario en caso de que no se modifique
            'user' => 'required|unique:usuarios,user,' . $this->input('id'),
            'rol_name' => 'required',
            'id_personal' => 'required',
        ];

        //si se esta editando el registro.. entonces el password ya no es obligatorio
        if ($this->isMethod('PUT')) {
            //empty => devuelve false cuando la variable NO  esta vacia y/o null
            //ejemplo password="1237596"
            if (empty($this->input('password')) == false) {
                $rules['password'] =  'min:8';
            }
        } else {
            $rules['password'] =  'required|min:8';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'id_personal.required' => 'El campo personal es requerido.',
            'rol_name.required' => 'El campo rol es requerido.',
            'password.required' => 'El campo contraseña es requerido.',
            'password.min' => 'El campo contraseña debe tener al menos 8 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator): HttpResponseException
    {


        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Verificar los campos!',
            'message_errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}//class
