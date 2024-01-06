<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('coutas', function (Blueprint $table) {
            $table->engine = 'InnoDB ROW_FORMAT=DYNAMIC';
            $table->id();
            $table->integer('num_couta');
            $table->boolean('couta_pagada'); //true=> significado que esta pagago, false=> significa que No esta pagado
            $table->date('fecha_maximo_pago_couta');
            $table->double('monto', 40, 2);
            $table->foreignId('id_contrato');
            $table->boolean('status');
            $table->timestamps();
            $table->foreign('id_contrato')->references('id')->on('contratos');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('coutas');
    }
};
