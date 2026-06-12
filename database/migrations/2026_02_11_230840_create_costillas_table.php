<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Costillas', function (Blueprint $table) {
            $table->integer('id_cos', true); // True activa el auto_increment
            $table->string('orden', 255)->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('id_cat');
            
            $table->foreign('id_cat')->references('id_cat')->on('CategoriasProd');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Costillas');
    }
};