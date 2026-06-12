<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('PizzasMariscos', function (Blueprint $table) {
            $table->integer('id_maris')->autoIncrement();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->integer('id_tamañop');
            $table->integer('id_cat');
            $table->foreign('id_tamañop')->references('id_tamañop')->on('TamanosPizza');
            $table->foreign('id_cat')->references('id_cat')->on('CategoriasProd');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('PizzasMariscos');
    }
};