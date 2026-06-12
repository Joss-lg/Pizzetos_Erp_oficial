<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('MetodosPago', function (Blueprint $table) {
            $table->integer('id_metpago')->autoIncrement();
            $table->string('metodo', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('MetodosPago');
    }
};