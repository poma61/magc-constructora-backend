<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('transacciones_pago_coutas', function (Blueprint $table) {
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
            $table->boolean('transaction_status'); //=> true => transaccion valido, false=>significa que la transaccion fue anulada
            $table->timestamps();
            $table->foreign('id_couta')->references('id')->on('coutas');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('transacciones_pago_coutas');
    }
};
