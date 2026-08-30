<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Apunta a tu archivo SQL de Pizzetos (Asegúrate de cambiar el nombre al correcto)
        $path = database_path('pizzetos_db.sql'); 
        
        // 2. Extraemos las credenciales dinámicas de Laravel Cloud
        $host = config('database.connections.mysql.host');
        $user = config('database.connections.mysql.username');
        $pass = config('database.connections.mysql.password');
        $db   = config('database.connections.mysql.database');

        // 3. Ejecutamos la importación nativa saltando la memoria de PHP
        exec("mysql -h {$host} -u {$user} -p'{$pass}' {$db} < {$path}");

        /* --- COMENTADO PARA EVITAR CONFLICTOS CON EL SQL ---
        $this->call([
            UsuarioAdminSeeder::class,
        ]);
        
        User::factory()->create([
            'nombre'   => 'Josue Lazaro',
            'nickName' => 'admin_pizzetos',
            'password' => bcrypt('admin123'), 
            'id_ca'    => 1,  
            'id_suc'   => 1,  
            'status'   => 1,
            'direccion'=> 'Dirección de prueba',
            'telefono' => '1234567890',
        ]);
        --------------------------------------------------- */
    }
}