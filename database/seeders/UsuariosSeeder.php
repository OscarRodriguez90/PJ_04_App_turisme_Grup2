<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuariosSeeder extends Seeder
{
    /**
     * Seed de la tabla tbl_usuarios (usuario admin).
     */
    public function run(): void
    {
        DB::table('tbl_usuarios')->insert([
            'username'   => 'admin',
            'nombre'     => 'Admin',
            'apellido1'  => 'Sistema',
            'apellido2'  => 'Principal',
            'email'      => 'admin@geoturismo.com',
            'password'   => '$2y$10$7R9rRjM2ZpDkH6H9GzE7ueWnK.Zk5E1fU6H6Q8YyG.R6K1W2Q6C6W',
            'id_rol'     => 1,
        ]);
    }
}
