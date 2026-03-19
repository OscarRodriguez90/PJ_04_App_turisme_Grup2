<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sala extends Model
{
    use HasFactory;

    protected $table = 'tbl_salas';
    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion', 'estado', 'fecha_inicio'];

    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'numero_equipo', 'id');
    }

    public function pruebas()
    {
        return $this->hasMany(Prueba::class, 'id_sala');
    }
}
