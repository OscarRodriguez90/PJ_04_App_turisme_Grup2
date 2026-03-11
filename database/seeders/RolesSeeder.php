<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Seed de la tabla tbl_roles.
     */
    public function run(): void
    {
        DB::table('tbl_roles')->insert([
            ['nombre_rol' => 'admin'],
            ['nombre_rol' => 'usuario'],
        ]);
    }
}
