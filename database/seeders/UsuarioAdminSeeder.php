<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioAdminSeeder extends Seeder
{
    public function run(): void
    {
        $id_suc = DB::table('Sucursales')->first()->id_suc ?? 1;

        if (!DB::table('Sucursales')->where('id_suc', $id_suc)->exists()) {
            $id_suc = DB::table('Sucursales')->insertGetId([
                'nombre' => 'Chalco Centro',
                'direccion' => 'Dirección Conocida',
                'telefono' => '5500000000',
                'status' => 1
            ]);
        }

        DB::table('Empleados')->insert([
            'nombre' => 'Administrador',
            'apellido' => 'Principal',
            'nickName' => 'admin', 
            'email' => 'admin@pizzetos.com',
            'password' => Hash::make('123'), 
            'id_suc' => $id_suc,
            'id_cargo' => 1, 
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Usuario Administrador creado exitosamente.');
        $this->command->warn('Usuario: admin');
        $this->command->warn('Contraseña: admin1234');
    }
}