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
        Schema::create('historial_de_pago_coutas', function (Blueprint $table) {
            $table->engine = 'InnoDB ROW_FORMAT=DYNAMIC';
            $table->id();
            $table->date('fecha_pago_couta');
            $table->double('monto', 40, 2);
            $table->foreignId('id_couta');
            $table->string('lugar');
            $table->string('servicio');
            $table->longText('nota')->nullable();
            $table->string('metodo_de_pago');
            $table->string('nombres');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->string('correo_electronico');
            $table->string('numero_de_contacto', 100);
            $table->boolean('pago_valido'); //=> true => pago correcto, false=>significa que el pago fue anulado
            $table->timestamps();
            $table->foreign('id_couta')->references('id')->on('coutas');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('historial_de_pagos_coutas');
    }
};
