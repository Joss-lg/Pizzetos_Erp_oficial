<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Direcciones', function (Blueprint $table) {
            $table->integer('id_dir')->autoIncrement();
            $table->integer('id_clie');
            $table->string('calle', 100);
            $table->string('manzana', 100)->nullable();
            $table->string('lote', 100)->nullable();
            $table->string('colonia', 100)->nullable();
            $table->string('referencia', 100)->nullable();
            $table->integer('status')->default(1);
            $table->foreign('id_clie')->references('id_clie')->on('Clientes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Direcciones');
    }
};