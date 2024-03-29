<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('detalle_contratos', function (Blueprint $table) {
            $table->engine = 'InnoDB ROW_FORMAT=DYNAMIC';
            $table->id();
            //primera datos del cliente y el acreedor
            //segunda
            $table->string('n_de_lote'); //debe ser string porque hay posibilidades de que tenga una letra
            $table->string('n_de_uv'); //debe ser string porque hay posibilidades de que tenga una letra
            $table->string('n_de_manzano');//debe ser string porque hay posibilidades de que tenga una letra
            $table->string('zona');
            $table->string('terreno_superficie');
            $table->double('terreno_valor_total_numeral', 40, 2)->nullable();
            $table->string('terreno_valor_total_literal')->nullable();
            $table->double('terreno_val_couta_inicial_numeral', 40, 2)->nullable();
            $table->double('terreno_val_couta_mensual_numeral', 40, 2)->nullable();
            $table->string('nombre_urbanizacion'); //debe ser string porque hay posibilidades de que tenga una letra
            $table->string('n_identificacion_terreno'); //debe ser string algunos no tienen numero entonces se coloca  'S/N=sin numero'
            //tecera
            $table->double('norte_medida_terreno', 20, 2);
            $table->string('norte_colinda_lote'); //colinda con lote NUMERO DE LOTE O NOMBRE DE LA CALLE 

            $table->double('sur_medida_terreno', 20, 2);
            $table->string('sur_colinda_lote'); //colinda con lote NUMERO DE LOTE O NOMBRE DE LA CALLE 

            $table->double('este_medida_terreno', 20, 2);
            $table->string('este_colinda_lote'); //colinda con lote NUMERO DE LOTE O NOMBRE DE LA CALLE 

            $table->double('oeste_medida_terreno', 20, 2);
            $table->string('oeste_colinda_lote'); //colinda con lote NUMERO DE LOTE O NOMBRE DE LA CALLE 
            //cuarta
            $table->string('construccion_descripcion');
            $table->double('construccion_superficie', 20, 2);
            //numero_identificacion_terreno
            $table->string('construccion_valor_total_literal');
            $table->double('construccion_valor_total_numeral', 40, 2);
            $table->integer('construccion_cantidad_meses_de_entrega');

            //quinta
            $table->string('construccion_val_couta_inicial_literal');
            $table->double('construccion_val_couta_inicial_numeral', 40, 2);

            $table->integer('cantidad_coutas_mensuales');
            $table->string('construccion_val_couta_mensual_literal');
            $table->double('construccion_val_couta_mensual_numeral', 40, 2);
            $table->date('fecha_cancelacion_coutas');

            //las tres primeras coutas son editables
            //para visualizar el la tabla del contrato que se generara
            $table->double('primera_val_couta_mensual_numeral', 40, 2); //verificar si aumentar la palabra 'construccion'
            $table->double('segunda_val_couta_mensual_numeral', 40, 2);
            $table->double('tercera_val_couta_mensual_numeral', 40, 2);
            $table->double('cuarta_val_couta_mensual_numeral', 40, 2);
            //sexta
            //construccion_valor_couta_inicial_numeral
            //construccion_valor_couta_inicial_literal
            //septima 
            //nombre El Acreedor
            //nombre cliente "Deudor"      
            $table->string('lugar_firma_contrato');
            $table->date('fecha_firma_contrato');

            $table->foreignId('id_contrato');
            $table->boolean('status');
            $table->timestamps();
            $table->foreign('id_contrato')->references('id')->on('contratos');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('detalle_contratos');
    }
};
