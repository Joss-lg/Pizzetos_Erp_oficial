<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('Pago', function (Blueprint $table) {
            $table->integer('id_pago', true); // True activa el auto_increment
            $table->integer('id_venta');
            $table->integer('id_metpago');
            $table->decimal('monto', 10, 2);
            $table->string('referencia', 100)->nullable();

            $table->foreign('id_venta')->references('id_venta')->on('Venta');
            $table->foreign('id_metpago')->references('id_metpago')->on('MetodosPago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Pago');
    }
};