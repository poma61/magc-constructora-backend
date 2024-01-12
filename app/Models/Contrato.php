<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

    protected $fillable = [
        'n_contrato',
        'descripcion',
        'archivo_pdf',
        'status',
    ];

    protected $hidden=[
        'updated_at',
        'status',
    ];
    
}
