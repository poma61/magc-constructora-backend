<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detalle_contratos', function (Blueprint $table) {
            $table->engine = 'InnoDB ROW_FORMAT=DYNAMIC';
            $table->id();
            //primera datos del cliente y el acreedor
            //segunda
            $table->string('n_de_lote');
            $table->string('n_de_uv');
            $table->string('zona');
            $table->string('superficie_terreno'); //opciones definidas en el formulario
            $table->double('valor_terreno_numeral', 40, 2)->nullable();
            $table->string('numero_distrito');
            $table->string('numero_identificacion_terreno'); //debe ser string algunos no tienen numero entonces se coloca  'S/N=sin numero'
            //tecera
            $table->string('norte_medida_terreno');
            $table->string('norte_colinda_lote'); //colinda con lote NUMERO DE LOTE O NOMBRE DE LA CALLE 

            $table->string('sur_medida_terreno');
            $table->string('sur_colinda_lote'); //colinda con lote NUMERO DE LOTE O NOMBRE DE LA CALLE 

            $table->string('este_medida_terreno');
            $table->string('este_colinda_lote'); //colinda con lote NUMERO DE LOTE O NOMBRE DE LA CALLE 

            $table->string('oeste_medida_terreno');
            $table->string('oeste_colinda_lote'); //colinda con lote NUMERO DE LOTE O NOMBRE DE LA CALLE 
            //cuarta
            $table->string('construccion_descripcion');
            $table->double('construccion_superficie_terreno');
            //numero_identificacion_terreno
            $table->string('construccion_valor_literal');
            $table->double('construccion_valor_numeral', 40, 2);

            //quinta
            $table->string('construccion_valor_couta_inicial_literal');
            $table->double('construccion_valor_couta_inicial_numeral', 40, 2);

            $table->string('construccion_valor_couta_mensual_literal');
            $table->double('construccion_valor_couta_mensual_numeral',40, 2);
            $table->integer('construccion_cantidad_couta_mensual');

            //las tres primeras coutas son editables
            //para visualizar el la tabla del contrato que se generara
            $table->double('primera_val_couta_mensual_numeral', 40, 2);//verificar si aumentar la palabra 'construccion'
            $table->double('segunda_val_couta_mensual_numeral', 40, 2);
            $table->double('tercera_val_couta_mensual_numeral', 40, 2);
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_contratos');
    }
};
