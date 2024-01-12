<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desarrolladora extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombres',
        'logo',
        'direccion',
        'descripcion',
        'correo_electronico',
        'status',
    ];

    protected $hidden = [
        'updated_at',
        'status',
    ];
}//class
