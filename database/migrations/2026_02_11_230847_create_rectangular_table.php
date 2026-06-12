<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('Rectangular', function (Blueprint $table) {
            $table->integer('id_rec', true); // El segundo parámetro true define auto_increment
            $table->integer('id_esp');
            $table->integer('id_cat');
            $table->decimal('precio', 10, 2)->nullable();
            
            $table->foreign('id_esp')->references('id_esp')->on('Especialidades');
            $table->foreign('id_cat')->references('id_cat')->on('CategoriasProd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Rectangular');
    }
};