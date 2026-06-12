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
        Schema::table('Gastos', function (Blueprint $table) {
            // Agregamos la columna id_emp, permitiendo nulos por los gastos antiguos
            $table->integer('id_emp')->nullable()->after('id_suc');

            // Si tienes la tabla Empleados, es buena práctica agregar la llave foránea
            $table->foreign('id_emp')->references('id_emp')->on('Empleados')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Gastos', function (Blueprint $table) {
            // Eliminamos la llave foránea y luego la columna
            $table->dropForeign(['id_emp']);
            $table->dropColumn('id_emp');
        });
    }
};