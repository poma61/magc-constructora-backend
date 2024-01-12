<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Couta extends Model
{
    use HasFactory;

    protected $fillable = [
        'num_couta',
        'fecha_maximo_pago_couta',
        'monto',
        'id_contrato',
        'status',
    ];

    protected $hidden = [
        'updated_at',
        'status',
    ];
    
}//class
