<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteHasContrato extends Model
{
    use HasFactory;

    protected $table = 'clientes_has_contratos';
    protected $fillable = [
        'id_contrato',
        'id_cliente',
    ];

    protected $hidden = [
        'updated_at',
    ];
}
