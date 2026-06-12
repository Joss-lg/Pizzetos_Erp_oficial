<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Especialidades', function (Blueprint $table) {
            $table->integer('id_esp')->autoIncrement();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Especialidades');
    }
};