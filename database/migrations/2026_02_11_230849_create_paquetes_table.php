<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Paquetes', function (Blueprint $table) {
            $table->integer('id_paquete', true);
            $table->string('nombre', 10);
            $table->string('descripcion', 100);
            $table->decimal('precio', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Paquetes');
    }
};