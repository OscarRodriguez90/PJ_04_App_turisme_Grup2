<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LugaresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // RESTAURANTES (Cat ID: 1)
        DB::table('tbl_lugares')->insert([
            ['nombre' => 'Mizuna Restaurante',              'descripcion' => 'Cocina japonesa de calidad.',              'direccion_completa' => 'Carrer de la Riera Blanca, 5',                  'latitud' => 41.37120000, 'longitud' => 2.12210000, 'icono' => null, 'id_categoria' => 1],
            ['nombre' => 'This & That Co.',                  'descripcion' => 'Gastronomía creativa y tapas.',            'direccion_completa' => 'Carrer d Amadeu Torner, 41',                    'latitud' => 41.36020000, 'longitud' => 2.12450000, 'icono' => null, 'id_categoria' => 1],
            ['nombre' => 'El Racó de l H',                   'descripcion' => 'Especialidad en brasa y cocina catalana.', 'direccion_completa' => 'Carrer de Bellvitge, 1',                        'latitud' => 41.35230000, 'longitud' => 2.11450000, 'icono' => null, 'id_categoria' => 1],
            ['nombre' => 'Restaurante Debut',                'descripcion' => 'Ambiente moderno y menús variados.',       'direccion_completa' => 'Plaça d Europa, 45',                            'latitud' => 41.35810000, 'longitud' => 2.12670000, 'icono' => null, 'id_categoria' => 1],
            ['nombre' => 'Mussol Glòries (L Hospitalet)',    'descripcion' => 'Tradición de masía catalana.',             'direccion_completa' => 'Av. de la Granvia de l Hospitalet, 75',         'latitud' => 41.35360000, 'longitud' => 2.12780000, 'icono' => null, 'id_categoria' => 1],
        ]);

        // COLEGIOS (Cat ID: 2)
        DB::table('tbl_lugares')->insert([
            ['nombre' => 'Jesuïtes Bellvitge - Joan XXIII',  'descripcion' => 'Centro educativo concertado.',                   'direccion_completa' => 'Avinguda de Mare de Déu de Bellvitge, 100', 'latitud' => 41.35140000, 'longitud' => 2.11580000, 'icono' => null, 'id_categoria' => 2],
            ['nombre' => 'Institut Can Vilumara',             'descripcion' => 'Instituto de educación secundaria histórico.',   'direccion_completa' => 'Avinguda de Josep Tarradellas i Joan, 153', 'latitud' => 41.36190000, 'longitud' => 2.10650000, 'icono' => null, 'id_categoria' => 2],
            ['nombre' => 'Escola Tecla Sala',                 'descripcion' => 'Escuela cristiana concertada.',                 'direccion_completa' => 'Carrer de Tecla Sala, 10',                 'latitud' => 41.36250000, 'longitud' => 2.11300000, 'icono' => null, 'id_categoria' => 2],
            ['nombre' => 'Institut Bellvitge',                'descripcion' => 'Instituto público en el barrio de Bellvitge.',  'direccion_completa' => 'Carrer d Ermita, 45',                      'latitud' => 41.34880000, 'longitud' => 2.11120000, 'icono' => null, 'id_categoria' => 2],
            ['nombre' => 'Escola Sant Josep - El Pi',         'descripcion' => 'Colegio en el centro de la ciudad.',            'direccion_completa' => 'Carrer d Enric Prat de la Riba, 100',      'latitud' => 41.36050000, 'longitud' => 2.10880000, 'icono' => null, 'id_categoria' => 2],
        ]);

        // GASOLINERAS (Cat ID: 3)
        DB::table('tbl_lugares')->insert([
            ['nombre' => 'Repsol Gran Via',            'descripcion' => 'Estación de servicio 24h.',              'direccion_completa' => 'Av. de la Granvia de l Hospitalet, 120', 'latitud' => 41.35200000, 'longitud' => 2.12300000, 'icono' => null, 'id_categoria' => 3],
            ['nombre' => 'Galp Hospitalet',            'descripcion' => 'Estación con tienda y lavado.',          'direccion_completa' => 'Carrer de la Riera dels Frares, 10',     'latitud' => 41.35550000, 'longitud' => 2.11500000, 'icono' => null, 'id_categoria' => 3],
            ['nombre' => 'Petroprix L Hospitalet',     'descripcion' => 'Gasolinera Low Cost.',                   'direccion_completa' => 'Avinguda de l Hospitalet, 50',           'latitud' => 41.36800000, 'longitud' => 2.10500000, 'icono' => null, 'id_categoria' => 3],
            ['nombre' => 'Shell L Hospitalet',         'descripcion' => 'Estación de servicio cerca de la Fira.', 'direccion_completa' => 'Carrer de les Ciències, 25',             'latitud' => 41.35010000, 'longitud' => 2.13100000, 'icono' => null, 'id_categoria' => 3],
            ['nombre' => 'BP L Hospitalet',            'descripcion' => 'Servicio rápido y eficiente.',           'direccion_completa' => 'Carrer de Santa Eulàlia, 150',           'latitud' => 41.36500000, 'longitud' => 2.12800000, 'icono' => null, 'id_categoria' => 3],
        ]);

        // HOTELES (Cat ID: 4)
        DB::table('tbl_lugares')->insert([
            ['nombre' => 'Hotel Hesperia Tower',              'descripcion' => 'Hotel de lujo con diseño icónico.',      'direccion_completa' => 'Avinguda de la Granvia de l Hospitalet, 144', 'latitud' => 41.34440000, 'longitud' => 2.10850000, 'icono' => null, 'id_categoria' => 4],
            ['nombre' => 'Hotel Renaissance Barcelona Fira',  'descripcion' => 'Famoso por su jardín vertical.',         'direccion_completa' => 'Plaça d Europa, 50',                         'latitud' => 41.35560000, 'longitud' => 2.12480000, 'icono' => null, 'id_categoria' => 4],
            ['nombre' => 'Hotel Porta Fira',                  'descripcion' => 'Torre roja diseñada por Toyo Ito.',      'direccion_completa' => 'Plaça d Europa, 45',                         'latitud' => 41.35450000, 'longitud' => 2.12600000, 'icono' => null, 'id_categoria' => 4],
            ['nombre' => 'Eurohotel Gran Via Fira',           'descripcion' => 'Próximo al centro comercial Gran Via 2.','direccion_completa' => 'Carrer de les Ciències, 98',                 'latitud' => 41.34900000, 'longitud' => 2.13300000, 'icono' => null, 'id_categoria' => 4],
            ['nombre' => 'Hotel Travelodge L Hospitalet',     'descripcion' => 'Alojamiento moderno y económico.',       'direccion_completa' => 'Carrer de la Botánica, 25',                  'latitud' => 41.34750000, 'longitud' => 2.12900000, 'icono' => null, 'id_categoria' => 4],
        ]);

        // HOSPITALES (Cat ID: 5)
        DB::table('tbl_lugares')->insert([
            ['nombre' => 'Hospital de Bellvitge',                 'descripcion' => 'Hospital universitario de referencia.', 'direccion_completa' => 'Carrer de la Residència, s/n',     'latitud' => 41.34580000, 'longitud' => 2.10750000, 'icono' => null, 'id_categoria' => 5],
            ['nombre' => 'Hospital de L Hospitalet',              'descripcion' => 'Centro hospitalario integral.',         'direccion_completa' => 'Avinguda de Josep Molins, 29',     'latitud' => 41.36950000, 'longitud' => 2.10220000, 'icono' => null, 'id_categoria' => 5],
            ['nombre' => 'Hospital Sant Joan de Déu (Sede)',      'descripcion' => 'Especializado en pediatría.',           'direccion_completa' => 'Passeig de Sant Joan de Déu, 2',   'latitud' => 41.38350000, 'longitud' => 2.10200000, 'icono' => null, 'id_categoria' => 5],
            ['nombre' => 'Hospital General de L Hospitalet',      'descripcion' => 'Consorci Sanitari Integral.',           'direccion_completa' => 'Carrer de l Escorxador, s/n',      'latitud' => 41.36800000, 'longitud' => 2.10100000, 'icono' => null, 'id_categoria' => 5],
            ['nombre' => 'Institut Català d Oncologia (ICO)',     'descripcion' => 'Centro oncológico especializado.',      'direccion_completa' => 'Gran Via de l Hospitalet, 199',    'latitud' => 41.34500000, 'longitud' => 2.10600000, 'icono' => null, 'id_categoria' => 5],
        ]);
    }
}
