<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Ingredientes', function (Blueprint $table) {
            $table->integer('id_ingrediente', true);
            $table->string('ingrediente', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Ingredientes');
    }
};