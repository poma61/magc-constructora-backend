<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
//add
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
{
    use  HasFactory, Notifiable;

    protected $fillable = [
        'user',
        'password',
        'status',
        'id_personal',
    ];

    protected $hidden = [
        'password',
        'status',
        'updated_at',
    ];

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function isPersonal()
    {
        //hace una consulta "select * from `personals` where `personals`.`id` = ?"
        //tomando el id_personal del modelo Usuario
        //return $this->belongsTo(Personal::class,'id_personal')->first();
        return  Usuario::join('usuario_roles', 'usuario_roles.id_user', '=', 'usuarios.id')
            ->join('roles', 'roles.id', '=', 'usuario_roles.id_role')
            ->join('personals', 'personals.id', '=', 'usuarios.id_personal')
            ->join('desarrolladoras', 'desarrolladoras.id', '=', 'personals.id_desarrolladora')
            ->select(
                'usuarios.user',
                'roles.rol_name',
                'personals.*',
                'desarrolladoras.nombres as desarrolladora'
            ) //no es necesario verificar el status porque en toda peticion en el middleware JwtAuthenticate verifica el status de los datos
            ->where('usuarios.id', Auth::user()->id)
            ->first();
    }

    // Nueva función para verificar si el personal tiene un rol específico
    public function hasRole(array $roles): bool
    {
        $user = $this->isPersonal(); // Obtén la información del usuario y sus roles
        // verificar si el valor de $user->rol_name se encuentra en el array $roles
        //devulve true si el valor de $user->rol_name esta en el array $roles
        return  in_array($user->rol_name, $roles);
    }
}//class
