<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('DetalleVenta', function (Blueprint $table) {
            $table->integer('id_detalle', true);
            $table->integer('id_venta');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2)->nullable();
            $table->integer('id_hamb')->nullable();
            $table->integer('id_cos')->nullable();
            $table->integer('id_alis')->nullable();
            $table->integer('id_spag')->nullable();
            $table->integer('id_papa')->nullable();
            
            // Campos JSON tal cual tu estructura original
            $table->json('id_rec')->nullable();
            $table->json('id_barr')->nullable();
            $table->integer('id_maris')->nullable();
            $table->integer('id_refresco')->nullable();
            $table->text('id_paquete')->nullable();
            $table->json('id_magno')->nullable();
            $table->integer('id_pizza')->nullable();
            $table->integer('status')->default(1);
            $table->json('ingredientes')->nullable();
            $table->integer('queso')->nullable();
            $table->json('pizza_mitad')->nullable();
            
            // Llaves foráneas con nombres de tablas corregidos (CamelCase)
            $table->foreign('id_venta')->references('id_venta')->on('Venta');
            $table->foreign('id_hamb')->references('id_hamb')->on('Hamburguesas');
            $table->foreign('id_cos')->references('id_cos')->on('Costillas');
            $table->foreign('id_alis')->references('id_alis')->on('Alitas');
            $table->foreign('id_spag')->references('id_spag')->on('Spaguetty');
            $table->foreign('id_papa')->references('id_papa')->on('OrdenDePapas');
            $table->foreign('id_maris')->references('id_maris')->on('PizzasMariscos');
            $table->foreign('id_refresco')->references('id_refresco')->on('Refrescos');
            $table->foreign('id_pizza')->references('id_pizza')->on('Pizzas');
        });

        // Trigger AFTER INSERT corregido (Nombres de tablas: PVersion y Venta)
        DB::unprepared('
            CREATE TRIGGER verificarDetallesCrear AFTER INSERT ON DetalleVenta
            FOR EACH ROW
            BEGIN
                UPDATE PVersion
                SET version = version + 1
                WHERE id_suc = (
                    SELECT id_suc
                    FROM Venta
                    WHERE id_venta = NEW.id_venta
                );
            END
        ');

        // Trigger AFTER UPDATE corregido
        DB::unprepared('
            CREATE TRIGGER verificarDetallesActualizar AFTER UPDATE ON DetalleVenta
            FOR EACH ROW
            BEGIN
                IF (
                    NEW.id_venta <> OLD.id_venta
                    OR NEW.cantidad <> OLD.cantidad
                    OR NOT (NEW.precio_unitario <=> OLD.precio_unitario)
                    OR NOT (NEW.id_hamb <=> OLD.id_hamb)
                    OR NOT (NEW.id_cos <=> OLD.id_cos)
                    OR NOT (NEW.id_alis <=> OLD.id_alis)
                    OR NOT (NEW.id_spag <=> OLD.id_spag)
                    OR NOT (NEW.id_papa <=> OLD.id_papa)
                    OR NOT (NEW.id_rec <=> OLD.id_rec)
                    OR NOT (NEW.id_barr <=> OLD.id_barr)
                    OR NOT (NEW.id_maris <=> OLD.id_maris)
                    OR NOT (NEW.id_refresco <=> OLD.id_refresco)
                    OR NOT (NEW.id_paquete <=> OLD.id_paquete)
                    OR NOT (NEW.id_magno <=> OLD.id_magno)
                    OR NOT (NEW.id_pizza <=> OLD.id_pizza)
                    OR NOT (NEW.status <=> OLD.status)
                    OR NOT (NEW.ingredientes <=> OLD.ingredientes)
                ) THEN
                    UPDATE PVersion
                    SET version = version + 1
                    WHERE id_suc = (
                        SELECT id_suc
                        FROM Venta
                        WHERE id_venta = NEW.id_venta
                    );
                END IF;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS verificarDetallesCrear');
        DB::unprepared('DROP TRIGGER IF EXISTS verificarDetallesActualizar');
        Schema::dropIfExists('DetalleVenta');
    }
};