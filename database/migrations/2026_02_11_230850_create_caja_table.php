<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Caja', function (Blueprint $table) {
            $table->integer('id_caja')->autoIncrement();
            $table->integer('id_suc');
            $table->integer('id_emp');
            $table->dateTime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            $table->decimal('monto_inicial', 10, 0);
            $table->decimal('monto_final', 10, 0)->nullable();
            $table->integer('status')->default(1);
            $table->string('observaciones_apertura', 255)->nullable();
            $table->string('observaciones_cierre', 255)->nullable();
            
            $table->foreign('id_suc')->references('id_suc')->on('Sucursal');
            $table->foreign('id_emp')->references('id_emp')->on('Empleados');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Caja');
    }
};