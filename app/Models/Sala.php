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

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo_sala',
        'estado',
    ];

    // numero_equipo in tbl_equipos stores the sala id (implicit link without FK)
    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class, 'numero_equipo', 'id');
    }

    public function pruebas(): HasMany
    {
        return $this->hasMany(Prueba::class, 'id_sala');
    }

    public function getCodigoSalaAttribute(): string
    {
        return str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}
