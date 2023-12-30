<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user');
            $table->foreignId('id_role');
            $table->boolean('status');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('usuarios');
            $table->foreign('id_role')->references('id')->on('roles');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_roles');
    }
};
