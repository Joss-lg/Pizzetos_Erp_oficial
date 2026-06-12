<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Venta', function (Blueprint $table) {
            $table->integer('id_venta', true);
            $table->integer('id_suc');
            $table->integer('mesa')->nullable();
            $table->timestamp('fecha_hora')->useCurrent();
            $table->decimal('total', 10, 2)->nullable();
            $table->integer('status')->default(0);
            $table->string('comentarios', 255)->nullable();
            $table->integer('tipo_servicio')->nullable();
            $table->string('nombreClie', 100)->nullable();
            $table->integer('id_caja');
            $table->string('detalles', 100)->nullable();
            
            $table->foreign('id_suc')->references('id_suc')->on('Sucursal');
            $table->foreign('id_caja')->references('id_caja')->on('Caja');
        });

        // Trigger AFTER INSERT
        DB::unprepared('
            CREATE TRIGGER verificarCrear AFTER INSERT ON Venta
            FOR EACH ROW
            BEGIN
                UPDATE PVersion
                SET version = version + 1
                WHERE id_suc = NEW.id_suc;
            END
        ');

        // Trigger AFTER UPDATE
        DB::unprepared('
            CREATE TRIGGER verificarVentaActualizar AFTER UPDATE ON Venta
            FOR EACH ROW
            BEGIN
                IF (
                    NEW.id_suc <> OLD.id_suc
                    OR NOT (NEW.mesa <=> OLD.mesa)
                    OR NEW.fecha_hora <> OLD.fecha_hora
                    OR NOT (NEW.total <=> OLD.total)
                    OR NOT (NEW.comentarios <=> OLD.comentarios)
                    OR NOT (NEW.tipo_servicio <=> OLD.tipo_servicio)
                    OR NOT (NEW.nombreClie <=> OLD.nombreClie)
                    OR NEW.id_caja <> OLD.id_caja
                    OR NOT (NEW.detalles <=> OLD.detalles)
                ) THEN
                    UPDATE PVersion
                    SET version = version + 1
                    WHERE id_suc = NEW.id_suc;
                END IF;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS verificarCrear');
        DB::unprepared('DROP TRIGGER IF EXISTS verificarVentaActualizar');
        Schema::dropIfExists('Venta');
    }
};