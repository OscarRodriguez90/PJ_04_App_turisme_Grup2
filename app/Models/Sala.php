<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sala extends Model
{
    use HasFactory;

    protected $table = 'tbl_salas';
    public $timestamps = false;

    protected $fillable = ['codigo_sala', 'estado'];

    // numero_equipo in tbl_equipos stores the sala id (implicit link without FK)
    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'numero_equipo', 'id');
    }
}
