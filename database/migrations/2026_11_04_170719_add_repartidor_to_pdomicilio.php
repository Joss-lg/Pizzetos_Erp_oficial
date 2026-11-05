<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('PDomicilio', function (Blueprint $table) {
            $table->unsignedInteger('id_repartidor')->nullable()->after('status');
            // 0 = sin asignar, 1 = en camino, 2 = entregado
            $table->tinyInteger('status_reparto')->default(0)->after('id_repartidor');
        });

        // Cargo Repartidor
        DB::table('Cargos')->insertOrIgnore([
            ['id_ca' => 3, 'nombre' => 'Repartidor'],
        ]);
    }

    public function down(): void
    {
        Schema::table('PDomicilio', function (Blueprint $table) {
            $table->dropColumn(['id_repartidor', 'status_reparto']);
        });

        DB::table('Cargos')->where('id_ca', 3)->delete();
    }
};
