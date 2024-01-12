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
        Schema::create('clientes_has_contratos', function (Blueprint $table) {
            //se creo la tabla intermedia porque un cliente puede firmar muchos contratos o tener muchos contratos
            //un contrato puede ser firmado por un cliente o dos clientes en el caso (marido y mujer), asi como tambien puede ser
            //firmado por mas de dos clientes (ejemplo 3 hermanos firman contrato)
            $table->engine = 'InnoDB ROW_FORMAT=DYNAMIC';
            $table->id();
            $table->foreignId('id_cliente');
            $table->foreignId('id_contrato');
            $table->timestamps();

            $table->foreign('id_cliente')->references('id')->on('clientes');
            $table->foreign('id_contrato')->references('id')->on('contratos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes_has_contratos');
    }
};
