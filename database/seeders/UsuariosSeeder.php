<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $passwordHash = Hash::make('password');

        // Admin
        DB::table('tbl_usuarios')->insert([
            'username' => 'admin',
            'nombre' => 'Admin',
            'apellido1' => 'Sistema',
            'apellido2' => 'Principal',
            'email' => 'admin@geoturismo.com',
            'password' => $passwordHash,
            'id_rol' => 1,
        ]);

        // Usuarios de ejemplo
        $usuarios = [
            ['username' => 'maria01',  'nombre' => 'María',  'apellido1' => 'García',    'apellido2' => 'López',     'email' => 'maria@geoturismo.com'],
            ['username' => 'carlos02', 'nombre' => 'Carlos', 'apellido1' => 'Martínez',  'apellido2' => 'Ruiz',      'email' => 'carlos@geoturismo.com'],
            ['username' => 'laura03',  'nombre' => 'Laura',  'apellido1' => 'Fernández', 'apellido2' => 'Sánchez',   'email' => 'laura@geoturismo.com'],
            ['username' => 'pedro04',  'nombre' => 'Pedro',  'apellido1' => 'López',     'apellido2' => 'Torres',    'email' => 'pedro@geoturismo.com'],
            ['username' => 'ana05',    'nombre' => 'Ana',    'apellido1' => 'Rodríguez', 'apellido2' => 'Moreno',    'email' => 'ana@geoturismo.com'],
            ['username' => 'jorge06',  'nombre' => 'Jorge',  'apellido1' => 'Hernández', 'apellido2' => 'Díaz',      'email' => 'jorge@geoturismo.com'],
            ['username' => 'lucia07',  'nombre' => 'Lucía',  'apellido1' => 'Jiménez',   'apellido2' => 'Álvarez',   'email' => 'lucia@geoturismo.com'],
            ['username' => 'david08',  'nombre' => 'David',  'apellido1' => 'Romero',    'apellido2' => 'Navarro',   'email' => 'david@geoturismo.com'],
            ['username' => 'elena09',  'nombre' => 'Elena',  'apellido1' => 'Torres',    'apellido2' => 'Domínguez', 'email' => 'elena@geoturismo.com'],
            ['username' => 'miguel10', 'nombre' => 'Miguel', 'apellido1' => 'Gil',       'apellido2' => 'Vázquez',   'email' => 'miguel@geoturismo.com'],
        ];

        foreach ($usuarios as $usuario) {
            DB::table('tbl_usuarios')->insert(array_merge($usuario, [
                'password' => $passwordHash,
                'id_rol' => 2,
            ]));
        }
    }
}
