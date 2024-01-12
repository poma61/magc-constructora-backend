<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'n_de_contacto',
        'correo_electronico',
        'ci',
        'ci_expedido',
        'direccion',
        'descripcion',
        'id_desarrolladora',
        'status',
    ];

    protected $hidden = [
        'updated_at',
        'status',
    ];
    
}//class
