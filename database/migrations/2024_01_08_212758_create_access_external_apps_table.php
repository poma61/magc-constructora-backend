<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('access_external_apps', function (Blueprint $table) {

            $table->engine = 'InnoDB ROW_FORMAT=DYNAMIC';
            $table->id();
            $table->string('access_token')->unique();
            $table->string('application_name');
            $table->timestamps();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_external_apps');
    }
};
