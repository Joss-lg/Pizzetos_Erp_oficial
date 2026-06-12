<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('PVersion', function (Blueprint $table) {
            $table->integer('id_pversion')->autoIncrement();
            $table->integer('id_suc');
            $table->bigInteger('version')->default(0);
            $table->foreign('id_suc')->references('id_suc')->on('Sucursal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('PVersion');
    }
};