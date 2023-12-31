<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//add
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthProfileRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $id = Auth::user()->id;
        return [
            // si estamos haciendo un update debe permitir el ingreso del mismo usuario en caso de que no se modifique
            'user' => "required|unique:usuarios,user,{$id}",
            'new_password' => 'required|min:8',
            'confirm_new_password' => 'required|same:new_password',
            'old_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Verificar si la contraseña antigua es correcta
                    if (!Hash::check($value, Auth::user()->password)) {
                        $fail('La contraseña antigua no es válida.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'user.required' =>  'El campo usuario es requerido.',
            'user.unique' =>  'El campo usuario ya ha sido tomado.',
            'old_password.required' => 'El campo contraseña anterior es requerido.',
            'new_password.required' => 'El campo contraseña nueva es requerido.',
            'new_password.min' => 'El campo contraseña nueva debe tener al menos 8 caracteres.',
            'confirm_new_password.required' => 'Las contraseñas no coinciden.',
            'confirm_new_password.same' => 'Las contraseñas no coinciden.',
    
        ];
    }

    protected function failedValidation(Validator $validator): HttpResponseException
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Verificar los campos solicitados!',
            'message_errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}//class
