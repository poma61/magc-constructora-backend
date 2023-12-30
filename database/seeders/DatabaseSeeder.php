<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Desarrolladora;
use App\Models\Personal;
use App\Models\Role;
use App\Models\Usuario;
use App\Models\UsuarioRole;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        Desarrolladora::create([
            'nombres' => 'Celina',
            'logo' => '/storage/img/desarrolladora/logo-celina.png',
            'direccion' => 'Santa Cruz',
            'descripcion' => 'ninguna',
            'status' => true,
        ]);

        Personal::create([
            'nombres' => 'Admin',
            'apellido_paterno' => 'ap paterno',
            'apellido_materno' => 'ap materno',
            'cargo' => 'Sin especificar',
            'ci' => 654321,
            'ci_expedido' => 'OR',
            'n_de_contacto' => 1234567,
            'direccion' => 'La Paz - Bolivia',
            'status' => true,
            'foto' => '/storage/img/personal/user.png',
            'id_desarrolladora' => 1,
        ]);

        Usuario::create([
            'user' => 'admin',
            'status' => true,
            'password' => '$2y$10$jjDb4siaEWs3Iw.sFqFwquRENoM/Lsi.IK6WL5L9fXF/x1GXKPfFq', //1234
            'id_personal' => 1,
        ]);

        Role::create([
            'rol_name' => 'administrador',
        ]);
        Role::create([
            'rol_name' => 'usuario',
        ]);

        UsuarioRole::create([
         'id_user'=>1,
         'id_role'=>1,
         'status'=>true,
        ]);
    }
}//class
