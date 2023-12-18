<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleContrato extends Model
{
    use HasFactory;

    protected $fillable = [
        'n_de_lote',
        'n_de_uv',
        'zona',
        'superficie_terreno',
        'numero_distrito',
        'numero_identificacion_terreno',
        'norte_medida_terreno',
        'norte_colinda_lote',
        'sur_medida_terreno',
        'sur_colinda_lote',
        'este_medida_terreno',
        'este_colinda_lote',
        'oeste_medida_terreno',
        'oeste_colinda_lote',
        'valor_construccion_literal',
        'valor_construccion_numeral',
        'valor_couta_inicial_literal',
        'valor_couta_inicial_numeral',
        'valor_couta_mensual_literal',
        'valor_couta_mensual_numeral',
        'primera_val_couta_mensual_numeral',
        'segunda_val_couta_mensual_numeral',
        'tercera_val_couta_mensual_numeral',
        'lugar_firma_contrato',
        'fecha_firma_contrato',
        'id_contrato',
        'status',
    ];

    protected $hidden = [
        'updated_at',
        'status',
    ];
}
