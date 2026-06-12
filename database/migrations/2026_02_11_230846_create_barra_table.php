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
        Schema::create('Barra', function (Blueprint $table) {
            $table->integer('id_barr', true); // El segundo parámetro true define auto_increment
            $table->integer('id_especialidad');
            $table->integer('id_cat');
            $table->decimal('precio', 10, 2);
            
            $table->foreign('id_especialidad')->references('id_esp')->on('Especialidades');
            $table->foreign('id_cat')->references('id_cat')->on('CategoriasProd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Barra');
    }
};