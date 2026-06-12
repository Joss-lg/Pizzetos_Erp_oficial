<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('CategoriasProd', function (Blueprint $table) {
            $table->integer('id_cat')->autoIncrement();
            $table->string('descripcion', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('CategoriasProd');
    }
};