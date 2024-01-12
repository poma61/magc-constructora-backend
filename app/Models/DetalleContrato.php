<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleContrato extends Model
{
    use HasFactory;

    protected $fillable = [
        'n_de_lote', //puede ser numero o string
        'n_de_uv', //puede ser numero o string
        'n_de_manzano', //puede ser numero o string
        'zona',
        'terreno_superficie',
        'terreno_valor_total_numeral', //puede ser nulo
        'terreno_valor_total_literal', //puede ser nulo
        'terreno_val_couta_inicial_numeral', //puede ser nulo
        'terreno_val_couta_mensual_numeral', //puede ser nulo
        'nombre_urbanizacion',
        'n_identificacion_terreno',//puede ser numero o string
        'norte_medida_terreno',
        'norte_colinda_lote',
        'sur_medida_terreno',
        'sur_colinda_lote',
        'este_medida_terreno',
        'este_colinda_lote',
        'oeste_medida_terreno',
        'oeste_colinda_lote',
        'construccion_descripcion',
        'construccion_superficie',
        'construccion_valor_total_literal',
        'construccion_valor_total_numeral',
        'construccion_cantidad_meses_de_entrega',
        'construccion_val_couta_inicial_literal',
        'construccion_val_couta_inicial_numeral',
        'construccion_val_couta_mensual_literal',
        'construccion_val_couta_mensual_numeral',
        'fecha_cancelacion_coutas',
        'cantidad_coutas_mensuales',
        'primera_val_couta_mensual_numeral',
        'segunda_val_couta_mensual_numeral',
        'tercera_val_couta_mensual_numeral',
        'cuarta_val_couta_mensual_numeral',
        'lugar_firma_contrato',
        'fecha_firma_contrato',
        'id_contrato',
        'status',
    ];

    protected $hidden = [
        'updated_at',
        'status',
    ];
}//class
