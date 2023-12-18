<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('historial_de_pagos', function (Blueprint $table) {
            $table->engine = 'InnoDB ROW_FORMAT=DYNAMIC';
            $table->id();
            $table->foreignId('id_cliente');
            $table->double('monto', 40, 2);
            $table->date('fecha_pago');
            $table->boolean('status');
            $table->timestamps();

            $table->foreign('id_cliente')->references('id')->on('clientes');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('historial_de_pagos');
    }
};
