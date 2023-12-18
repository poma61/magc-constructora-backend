<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
    ];

    public function getJWTIdentifier():mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims():array
    {
        return [];
    }
   
    public function isPersonal(){
    //hace una consulta "select * from `personals` where `personals`.`id` = ?"
    //tomando el id_personal del modelo Usuario
      return $this->belongsTo(Personal::class,'id_personal')->first();
    }

}//class
