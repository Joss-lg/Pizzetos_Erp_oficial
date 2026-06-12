<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Empleados', function (Blueprint $table) {
            $table->integer('id_emp')->autoIncrement();
            $table->string('nombre', 255);
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->integer('id_ca');
            $table->integer('id_suc');
            $table->string('nickName', 50)->unique()->nullable();
            $table->string('password', 255)->nullable();
            $table->boolean('status');
            
            $table->foreign('id_ca')->references('id_ca')->on('Cargos');
            $table->foreign('id_suc')->references('id_suc')->on('Sucursal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Empleados');
    }
};