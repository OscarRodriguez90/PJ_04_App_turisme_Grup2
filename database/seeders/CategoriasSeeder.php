<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriasSeeder extends Seeder
{
    /**
     * Seed de la tabla tbl_categorias.
     */
    public function run(): void
    {
        DB::table('tbl_categorias')->insert([
            ['nombre' => 'Restaurantes', 'icono_url' => 'restaurante.png', 'color_marcador' => '#E74C3C'],
            ['nombre' => 'Colegios',     'icono_url' => 'colegio.png',     'color_marcador' => '#3498DB'],
            ['nombre' => 'Gasolineras',  'icono_url' => 'gasolinera.png',  'color_marcador' => '#F1C40F'],
            ['nombre' => 'Hoteles',      'icono_url' => 'hotel.png',       'color_marcador' => '#9B59B6'],
            ['nombre' => 'Hospitales',   'icono_url' => 'hospital.png',    'color_marcador' => '#2ECC71'],
        ]);
    }
}
