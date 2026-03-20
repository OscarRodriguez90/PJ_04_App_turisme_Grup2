<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Sala;
use App\Models\Lugar;
use App\Models\Prueba;

class GimcanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear la Primera Sala
        $sala1 = Sala::create([
            'nombre' => 'Ruta por L\'Hospitalet',
            'descripcion' => 'Una ruta histórica y comercial por los puntos más emblemáticos de la ciudad.',
            'estado' => 'disponible',
        ]);

        // 2. Definir los retos de la primera sala
        $retosRutaHosp = [
            [
                'lugar' => 'Cervecerías La Sureña y 100 Montaditos',
                'pregunta' => '¿Cuál es el número que da nombre a una de las famosas franquicias de este local?',
                'respuesta' => '100',
                'pista' => 'Está en el propio nombre del establecimiento.'
            ],
            [
                'lugar' => 'Tecla Sala',
                'pregunta' => '¿Qué tipo de centro cultural y educativo es este edificio histórico?',
                'respuesta' => 'Biblioteca',
                'pista' => 'Es un lugar donde se guardan muchos libros y se puede estudiar.'
            ],
            [
                'lugar' => 'CAP Just Oliveras',
                'pregunta' => '¿A qué tipo de centro de salud corresponden las siglas de este edificio?',
                'respuesta' => 'CAP',
                'pista' => 'Centro de Atención Primaria.'
            ],
            [
                'lugar' => 'Can Vilumara',
                'pregunta' => '¿Qué formación se imparte en este centro además de la ESO?',
                'respuesta' => 'Bachillerato',
                'pista' => 'Es la etapa educativa que viene después de la ESO.'
            ],
            [
                'lugar' => 'Gasolinera Galp',
                'pregunta' => '¿De qué color es el logotipo principal de esta cadena de gasolineras?',
                'respuesta' => 'Naranja',
                'pista' => 'Es un color cálido, muy común en los cítricos.'
            ],
        ];

        $this->insertarRetos($sala1->id, $retosRutaHosp);

        // ── SEGUNDA SALA: BELLVITGE ───────────────────────────────────────────
        
        $sala2 = Sala::create([
            'nombre' => 'Bellvitge Tour',
            'descripcion' => 'Descubre los puntos clave del barrio de Bellvitge, desde la facultad hasta sus bares más icónicos.',
            'estado' => 'disponible',
        ]);

        $retosBellvitge = [
            [
                'lugar' => 'Cafeteria Facultat de Medicina i Ciències de la Salut',
                'pregunta' => '¿A qué universidad pertenece esta facultad de medicina?',
                'respuesta' => 'UB',
                'pista' => 'Es la Universidad de Barcelona.'
            ],
            [
                'lugar' => 'Hospital de Bellvitge',
                'pregunta' => '¿Qué siglas se usan para referirse a este Hospital Universitari?',
                'respuesta' => 'HUB',
                'pista' => 'Hospital Universitari de Bellvitge.'
            ],
            [
                'lugar' => 'Hotel Hesperia Tower',
                'pregunta' => '¿Qué elemento circular corona la parte superior de esta gran torre?',
                'respuesta' => 'Cúpula',
                'pista' => 'Es una estructura redondeada en la cima del edificio.'
            ],
            [
                'lugar' => 'Galp direccion Barcelona',
                'pregunta' => '¿En qué autovía se encuentra situada esta estación de servicio?',
                'respuesta' => 'C-31',
                'pista' => 'Es la autovía de Castelldefels.'
            ],
            [
                'lugar' => 'Alonsos Cafe',
                'pregunta' => '¿En qué avenida de Bellvitge se encuentra este emblemático bar?',
                'respuesta' => 'Mare de Déu de Bellvitge',
                'pista' => 'Lleva el nombre de la patrona del barrio.'
            ],
        ];

        $this->insertarRetos($sala2->id, $retosBellvitge);
    }

    /**
     * Helper para insertar retos evitando código duplicado
     */
    private function insertarRetos($salaId, $retos)
    {
        foreach ($retos as $index => $data) {
            $lugar = Lugar::where('nombre', $data['lugar'])->first();

            if ($lugar) {
                Prueba::create([
                    'id_sala' => $salaId,
                    'id_lugar' => $lugar->id,
                    'orden' => $index + 1,
                    'pregunta' => $data['pregunta'],
                    'respuesta_correcta' => $data['respuesta'],
                    'pista' => $data['pista'],
                ]);
            }
        }
    }
}
