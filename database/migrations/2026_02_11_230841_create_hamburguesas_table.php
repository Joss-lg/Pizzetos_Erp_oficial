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
        Schema::create('Hamburguesas', function (Blueprint $table) {
            $table->integer('id_hamb', true); // El segundo parámetro true define auto_increment
            $table->string('paquete', 255)->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('id_cat');
            
            $table->foreign('id_cat')->references('id_cat')->on('CategoriasProd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Hamburguesas');
    }
};