<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prueba extends Model
{
    use HasFactory;

    protected $table = 'tbl_pruebas';

    public $timestamps = false;

    protected $fillable = [
        'id_lugar',
        'orden',
        'pregunta',
        'respuesta_correcta',
        'pista_siguiente',
    ];

    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar');
    }
}
