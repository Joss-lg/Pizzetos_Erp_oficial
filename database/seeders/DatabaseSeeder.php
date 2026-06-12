<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
   public function run(): void
    {
      $this->call([
            UsuarioAdminSeeder::class,
        ]);
        
        User::factory()->create([
            'nombre'   => 'Josue Lazaro',
            'nickName' => 'admin_pizzetos',
            'password' => bcrypt('admin123'), // O la contraseña que prefieras
            'id_ca'    => 1,  // IMPORTANTE: Asegúrate de que el cargo ID 1 exista
            'id_suc'   => 1,  // IMPORTANTE: Asegúrate de que la sucursal ID 1 exista
            'status'   => 1,
            'direccion'=> 'Dirección de prueba',
            'telefono' => '1234567890',
        ]);
    }
}
