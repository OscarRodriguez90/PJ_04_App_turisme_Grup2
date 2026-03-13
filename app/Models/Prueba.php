<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prueba extends Model
{
    use HasFactory;

    protected $table = 'tbl_retos';

    public $timestamps = false;

    protected $fillable = [
        'id_sala',
        'id_lugar',
        'orden',
        'pista',
        'pregunta',
        'respuesta_correcta',
    ];

    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar');
    }
}
