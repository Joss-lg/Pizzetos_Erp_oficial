<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TamanosRefrescos', function (Blueprint $table) {
            $table->integer('id_tamano')->autoIncrement();
            $table->string('tamano', 50);
            $table->decimal('precio', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TamanosRefrescos');
    }
};