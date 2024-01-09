<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaccionPagoCouta extends Model
{
    use HasFactory;
    protected $table = 'transacciones_pago_coutas';

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
        'transaction_status',
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
        'transaction_status',
    ];
}//class
