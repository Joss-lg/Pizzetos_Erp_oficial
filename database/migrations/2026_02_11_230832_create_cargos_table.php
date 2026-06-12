<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Cargos', function (Blueprint $table) {
            $table->integer('id_ca')->autoIncrement();
            $table->string('nombre', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Cargos');
    }
};