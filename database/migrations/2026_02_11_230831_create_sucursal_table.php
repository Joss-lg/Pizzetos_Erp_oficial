<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Sucursal', function (Blueprint $table) {
            $table->integer('id_suc')->autoIncrement();
            $table->string('nombre', 255);
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Sucursal');
    }
};