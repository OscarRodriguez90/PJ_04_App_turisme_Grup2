<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tbl_categorias')->insert([
            ['nombre' => 'Restaurantes', 'icono' => null, 'icono_url' => 'bi bi-shop',              'color_marcador' => '#E74C3C'],
            ['nombre' => 'Colegios',     'icono' => null, 'icono_url' => 'bi bi-book',              'color_marcador' => '#3498DB'],
            ['nombre' => 'Gasolineras',  'icono' => null, 'icono_url' => 'bi bi-fuel-pump',         'color_marcador' => '#F1C40F'],
            ['nombre' => 'Hoteles',      'icono' => null, 'icono_url' => 'bi bi-building',          'color_marcador' => '#9B59B6'],
            ['nombre' => 'Hospitales',   'icono' => null, 'icono_url' => 'bi bi-hospital',          'color_marcador' => '#2ECC71'],
        ]);
    }
}
