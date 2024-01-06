<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialPagoCouta extends Model
{
    use HasFactory;

    protected $table = 'historial_de_pagos_coutas';

    protected $fillable = [
        'fecha_pago_couta',
        'monto',
        'id_couta',
        'lugar',
        'servicio',
        'nota',
        'metodo_de_pago',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'correo_electronico',
        'numero_de_contacto',
        'pago_valido',
    ];

    protected $hidden = [
        'updated_at',
        'pago_valido',
    ];
}//class
