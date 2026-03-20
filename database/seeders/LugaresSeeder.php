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
            ['nombre' => 'Cervecerías La Sureña y 100 Montaditos',                          'descripcion' => 'Cervecería y restaurante de tapas y montaditos.',                                                                  'direccion_completa' => 'L Hospitalet de Llobregat, Barcelona',                                                    'latitud' => 41.36007700404991, 'longitud' => 2.1018881698980865, 'icono' => null, 'imagen' => null, 'id_categoria' => 1],
            ['nombre' => 'McDonald s',                                                      'descripcion' => 'Cadena de restaurantes de comida rápida de larga trayectoria, famosa por sus hamburguesas y patatas fritas.',         'direccion_completa' => 'Rambla de la Marina, 349, 08907 L Hospitalet de Llobregat, Barcelona',                  'latitud' => 41.352968962484226, 'longitud' => 2.105614467506085, 'icono' => null, 'imagen' => null, 'id_categoria' => 1],
            ['nombre' => 'Alonsos Cafe',                                                    'descripcion' => 'Bar de barrio de toda la vida',                                                                                       'direccion_completa' => 'Av. Mare de Déu de Bellvitge, 86, 08907 L Hospitalet de Llobregat, Barcelona',          'latitud' => 41.34930137881786, 'longitud' => 2.107515656178773, 'icono' => null, 'imagen' => null, 'id_categoria' => 1],
            ['nombre' => 'Cafeteria Facultat de Medicina i Ciències de la Salut',           'descripcion' => 'Cafeteria de la facultad de medicina de Barcelona',                                                                   'direccion_completa' => 'Carrer de la Feixa Llarga, s/n, 08907 L Hospitalet de Llobregat, Barcelona',            'latitud' => 41.346417719403256, 'longitud' => 2.106674105677844, 'icono' => null, 'imagen' => null, 'id_categoria' => 1],
            ['nombre' => 'UD Unificación Bellvitge',                                        'descripcion' => 'Bar de la Union Deportiva Unificación Bellvitge',                                                                     'direccion_completa' => 'Trav. Industrial, 50, 08907 L Hospitalet de Llobregat, Barcelona',                      'latitud' => 41.348215717963065, 'longitud' => 2.1027642974930942, 'icono' => null, 'imagen' => null, 'id_categoria' => 1],
        ]);

        // COLEGIOS (Cat ID: 2)
        DB::table('tbl_lugares')->insert([
            ['nombre' => 'Jesuïtes Bellvitge - Joan XXIII',  'descripcion' => 'Centro educativo concertado.',                   'direccion_completa' => 'Avinguda de Mare de Déu de Bellvitge, 100',                                'latitud' => 41.350096632798376, 'longitud' => 2.1072867873165846, 'icono' => null, 'imagen' => null, 'id_categoria' => 2],
            ['nombre' => 'Can Vilumara',                      'descripcion' => 'Centro educativo de la zona de Can Vilumara.',  'direccion_completa' => 'L Hospitalet de Llobregat, Barcelona',                                    'latitud' => 41.36331228837905, 'longitud' => 2.103945688673642, 'icono' => null, 'imagen' => null, 'id_categoria' => 2],
            ['nombre' => 'Tecla Sala',                         'descripcion' => 'Centro educativo de referencia en la zona.',     'direccion_completa' => 'L Hospitalet de Llobregat, Barcelona',                                    'latitud' => 41.3606070830524, 'longitud' => 2.0984641878084322, 'icono' => null, 'imagen' => null, 'id_categoria' => 2],
            ['nombre' => 'Colegio Xaloc',                      'descripcion' => 'Colegio concertado.',                           'direccion_completa' => 'Carrer Can Trias, 4, 08902 L Hospitalet de Llobregat, Barcelona',         'latitud' => 41.3556407816545, 'longitud' => 2.1213729327509245, 'icono' => null, 'imagen' => null, 'id_categoria' => 2],
            ['nombre' => 'Col·legi Pineda',                   'descripcion' => 'Colegio concertado.',                             'direccion_completa' => 'Carrer dels Joncs, 1, 08902 LHospitalet de Llobregat, Barcelona',        'latitud' => 41.35727858644354, 'longitud' => 2.1197124918964114, 'icono' => null, 'imagen' => null, 'id_categoria' => 2],
        ]);

        // GASOLINERAS (Cat ID: 3)
        DB::table('tbl_lugares')->insert([
            ['nombre' => 'Repsol Gran Via 2',            'descripcion' => 'Estación de servicio 24h.',              'direccion_completa' => 'Carrer de Salvador Espriu, 59, 08908 L Hospitalet de Llobregat, Barcelona',             'latitud' => 41.353976499303286, 'longitud' => 2.121987004747228, 'icono' => null, 'imagen' => null, 'id_categoria' => 3],
            ['nombre' => 'Galp direccion Castelldefels', 'descripcion' => 'Estación con tienda.',                    'direccion_completa' => '08907 LHospitalet de Llobregat, Barcelona',                                            'latitud' => 41.34594695258088, 'longitud' => 2.109462181015458, 'icono' => null, 'imagen' => null, 'id_categoria' => 3],
            ['nombre' => 'Galp direccion Barcelona',     'descripcion' => 'Gasolinera Low Cost.',                   'direccion_completa' => 'Autovia De Castelldefels, Km 2, 7, 08904 L Hospitalet de Llobregat, Barcelona',         'latitud' => 41.34649729214704, 'longitud' => 2.1117215926692374, 'icono' => null, 'imagen' => null, 'id_categoria' => 3],
            ['nombre' => 'Repsol Bellvitge',             'descripcion' => 'Estación de servicio cerca de la Fira.', 'direccion_completa' => 'Rambla de la Marina, 428, 08907 L Hospitalet de Llobregat, Barcelona',                  'latitud' => 41.353937235611696, 'longitud' => 2.1065444846918506, 'icono' => null, 'imagen' => null, 'id_categoria' => 3],
            ['nombre' => 'Gasolinera Galp',              'descripcion' => 'Estación de servicio con tienda.',       'direccion_completa' => 'L Hospitalet de Llobregat, Barcelona',                                                    'latitud' => 41.36157006153961, 'longitud' => 2.1063446245633974, 'icono' => null, 'imagen' => null, 'id_categoria' => 3],
        ]);

        // HOTELES (Cat ID: 4)
        DB::table('tbl_lugares')->insert([
            ['nombre' => 'Hotel Hesperia Tower',              'descripcion' => 'Hotel de lujo con diseño icónico.',      'direccion_completa' => 'Avinguda de la Granvia de l Hospitalet, 144, 08908 L Hospitalet de Llobregat, Barcelona', 'latitud' => 41.346379614964476, 'longitud' => 2.1086705547470843, 'icono' => null, 'imagen' => null, 'id_categoria' => 4],
            ['nombre' => 'Hotel Renaissance Barcelona Fira',  'descripcion' => 'Famoso por su jardín vertical.',         'direccion_completa' => 'Plaça d Europa, 50, 08908 L Hospitalet de Llobregat, Barcelona',                          'latitud' => 41.356391311112596, 'longitud' => 2.1231322287420307, 'icono' => null, 'imagen' => null, 'id_categoria' => 4],
            ['nombre' => 'Hotel Porta Fira',                  'descripcion' => 'Torre roja diseñada por Toyo Ito.',      'direccion_completa' => 'Plaça d Europa, 45, 08908 L Hospitalet de Llobregat, Barcelona',                          'latitud' => 41.35503083266921, 'longitud' => 2.125462839622888, 'icono' => null, 'imagen' => null, 'id_categoria' => 4],
            ['nombre' => 'Eurohotel Gran Via Fira',           'descripcion' => 'Próximo al centro comercial Gran Via 2.','direccion_completa' => 'Pl. d Europa, 33, 08908 L Hospitalet de Llobregat, Barcelona',                            'latitud' => 41.35582954933725, 'longitud' => 2.1270886709065073, 'icono' => null, 'imagen' => null, 'id_categoria' => 4],
            ['nombre' => 'Hotel Travelodge L Hospitalet',     'descripcion' => 'Alojamiento moderno y económico.',       'direccion_completa' => 'Carrer de la Botànica, 25, 08908 L Hospitalet de Llobregat, Barcelona',                   'latitud' => 41.35273638169549, 'longitud' => 2.133421154747318, 'icono' => null, 'imagen' => null, 'id_categoria' => 4],
        ]);

        // HOSPITALES (Cat ID: 5)
        DB::table('tbl_lugares')->insert([
            ['nombre' => 'Hospital de Bellvitge',                 'descripcion' => 'Hospital universitario de referencia.', 'direccion_completa' => 'Carrer de la Feixa Llarga, s/n, 08907 L Hospitalet de Llobregat, Barcelona',                   'latitud' => 41.345100652907554, 'longitud' => 2.104559031289106, 'icono' => null, 'imagen' => null, 'id_categoria' => 5],
            ['nombre' => 'Hospital Oncológico de Bellvitge',      'descripcion' => 'Centro hospitalario oncologico.',       'direccion_completa' => 'Avinguda de la Granvia de l’Hospitalet, 199, 08908 L Hospitalet de Llobregat, Barcelona',      'latitud' => 41.34485177333675, 'longitud' => 2.110599258355982, 'icono' => null, 'imagen' => null, 'id_categoria' => 5],
            ['nombre' => 'CAP Just Oliveras',                     'descripcion' => 'Centro de atención primaria.',           'direccion_completa' => 'L Hospitalet de Llobregat, Barcelona',                                                       'latitud' => 41.36279833604996, 'longitud' => 2.100946416200065, 'icono' => null, 'imagen' => null, 'id_categoria' => 5],
            ['nombre' => 'Hospital de Sant Joan Despí',      'descripcion' => 'Consorci Sanitari Integral.',                 'direccion_completa' => 'Carrer d Oriol Martorell, 12, 08970 Sant Joan Despí, Barcelona',                              'latitud' => 41.373900188941946, 'longitud' => 2.0722960877164476, 'icono' => null, 'imagen' => null, 'id_categoria' => 5],
            ['nombre' => 'Hospital Universitario Dexeus',     'descripcion' => 'Centro oncológico especializado.',           'direccion_completa' => 'Carrer de Sabino Arana, 19 Ed, Les Corts, 08028 Barcelona',                                   'latitud' => 41.38635637017762, 'longitud' => 2.1256586555022143, 'icono' => null, 'imagen' => null, 'id_categoria' => 5],
        ]);
    }
}
