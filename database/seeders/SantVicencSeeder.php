<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sala;
use App\Models\Lugar;
use App\Models\Prueba;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;

class SantVicencSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear una nueva categoría para la prueba
        $categoria = Categoria::firstOrCreate(
            ['nombre' => 'Exploración Sant Vicenç'],
            [
                'icono_url' => 'bi bi-geo-alt-fill',
                'color_marcador' => '#0ea5a4'
            ]
        );

        // 2. Crear la Sala de la Gimcana
        $sala = Sala::create([
            'nombre' => 'Descubre Sant Vicenç 08620',
            'descripcion' => 'Una ruta de prueba por los puntos emblemáticos cerca de Anselm Clavé.',
            'estado' => 'disponible',
        ]);

        // 3. Definir los lugares y retos
        // Coordenadas aproximadas cerca de Anselm Clavé 47 (41.3917, 2.0125)
        // Todas con separación > 150m
        $datos = [
            [
                'nombre' => 'Biblioteca Municipal Les Voltes',
                'descripcion' => 'Biblioteca pública en el centro del municipio.',
                'direccion' => 'Carrer d\'Anselm Clavé, 10, 08620 Sant Vicenç dels Horts',
                'lat' => 41.3922,
                'lng' => 2.0108,
                'pregunta' => '¿Qué nombre recibe esta biblioteca municipal?',
                'respuesta' => 'Les Voltes',
                'pista' => 'El nombre está en la fachada principal y significa "los arcos".'
            ],
            [
                'nombre' => 'Estació Can Ros (FGC)',
                'descripcion' => 'Estación de tren de los Ferrocarriles de la Generalitat.',
                'direccion' => 'Carrer de Mossèn Jacint Verdaguer, 08620 Sant Vicenç dels Horts',
                'lat' => 41.3955,
                'lng' => 2.0135,
                'pregunta' => '¿Cómo se llama esta estación de los Ferrocarrils de la Generalitat de Catalunya?',
                'respuesta' => 'Can Ros',
                'pista' => 'Es una de las dos estaciones del municipio, la más al norte.'
            ],
            [
                'nombre' => 'Parc de la Foneria',
                'descripcion' => 'Espacio verde y zona recreativa.',
                'direccion' => 'Carrer de la Foneria, 08620 Sant Vicenç dels Horts',
                'lat' => 41.3920,
                'lng' => 2.0160,
                'pregunta' => '¿Cuál es el nombre de este parque industrial convertido en zona verde?',
                'respuesta' => 'Foneria',
                'pista' => 'Hace referencia a la antigua fundición que había en este lugar.'
            ],
            [
                'nombre' => 'CAP El Serral',
                'descripcion' => 'Centro de Atención Primaria del municipio.',
                'direccion' => 'Carrer de l\'Esmalt, s/n, 08620 Sant Vicenç dels Horts',
                'lat' => 41.3885,
                'lng' => 2.0165,
                'pregunta' => '¿Cómo se llama este centro de salud (CAP)?',
                'respuesta' => 'El Serral',
                'pista' => 'Lleva el nombre del barrio o zona donde se encuentra.'
            ],
            [
                'nombre' => 'Plaça de Narcís Lunes',
                'descripcion' => 'Plaza céntrica con monumentos históricos.',
                'direccion' => 'Plaça de Narcís Lunes, 08620 Sant Vicenç dels Horts',
                'lat' => 41.3895,
                'lng' => 2.0120,
                'pregunta' => '¿A quién está dedicada esta céntrica plaza de Sant Vicenç?',
                'respuesta' => 'Narcís Lunes',
                'pista' => 'Búscalo en las placas de la plaza o en el monumento central.'
            ],
        ];

        foreach ($datos as $index => $item) {
            // Crear el Lugar
            $lugar = Lugar::create([
                'nombre' => $item['nombre'],
                'descripcion' => $item['descripcion'],
                'direccion_completa' => $item['direccion'],
                'latitud' => $item['lat'],
                'longitud' => $item['lng'],
                'id_categoria' => $categoria->id,
            ]);

            // Crear la Prueba asociada a la Sala y el Lugar
            Prueba::create([
                'id_sala' => $sala->id,
                'id_lugar' => $lugar->id,
                'orden' => $index + 1,
                'pregunta' => $item['pregunta'],
                'respuesta_correcta' => $item['respuesta'],
                'pista' => $item['pista'],
            ]);
        }
    }
}
