<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Insertamos el Cargo (quitamos los timestamps que dan error)
        // Si ya existe el ID 1, lo ignoramos para que no falle por duplicado
        $cargoExiste = DB::table('cargos')->where('id_ca', 1)->first();
        
        if (!$cargoExiste) {
            DB::table('cargos')->insert([
                'id_ca' => 1,
                'nombre' => 'Administrador',
            ]);
        }

        // 2. Buscamos o creamos la sucursal (Tabla: Sucursal)
        $sucursal = DB::table('Sucursal')->first();
        $id_suc = $sucursal ? $sucursal->id_suc : DB::table('Sucursal')->insertGetId([
            'nombre' => 'Miraflores',
            'direccion' => 'Dirección Conocida',
            'telefono' => '5500000000',
        ]);

        // 3. Insertamos el empleado (Tabla: Empleados)
        // Nota: Asegúrate de que los nombres de las columnas sean id_ca e id_suc
        DB::table('Empleados')->insert([
            'nombre' => 'Josue Lazaro',
            'direccion' => 'Dirección de prueba',
            'telefono' => '1234567890',
            'id_ca' => 1, 
            'id_suc' => $id_suc,
            'nickName' => 'admin@pizzetos.com',
            'password' => Hash::make('password'), 
            'status' => 1,
        ]);

        $this->command->info('¡Perfecto! Se creó el cargo y el administrador sin errores de timestamps.');
    }
}