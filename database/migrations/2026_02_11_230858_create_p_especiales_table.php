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
        Schema::create('PEspeciales', function (Blueprint $table) {
            $table->integer('id_pespeciales', true);
            $table->integer('id_venta');
            $table->integer('id_dir')->nullable();
            $table->integer('id_clie')->nullable();
            $table->decimal('anticipo', 10, 2)->default(0); 
            $table->datetime('fecha_creacion')->useCurrent();
            $table->datetime('fecha_entrega')->nullable();
            $table->integer('status')->default(1);

            $table->foreign('id_venta')->references('id_venta')->on('Venta');
            $table->foreign('id_dir')->references('id_dir')->on('Direcciones');
            $table->foreign('id_clie')->references('id_clie')->on('Clientes');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('PEspeciales');
    }
};