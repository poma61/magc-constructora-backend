<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Personal;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        Personal::create([
            'nombres' => 'Admin',
            'apellido_paterno' => 'ap paterno',
            'apellido_materno' => 'ap materno',
            'cargo' => 'Sin especificar',
            'ci' => 654321,
            'ci_expedido' => 'OR',
            'telefono' => 1234567,
            'correo_electronico' => "system@gmail.com",
            'direccion' => 'La Paz - Bolivia',
            'status' => true,
            'foto' => 'storage/imagenes/img-user.jpg',
        ]);

        Usuario::create([
            'user' => 'admin@gmail.com',
            'status' => true,
            'password' => '$2y$10$jjDb4siaEWs3Iw.sFqFwquRENoM/Lsi.IK6WL5L9fXF/x1GXKPfFq', //1234
            'id_personal' => 1,
        ]);
    }
}
