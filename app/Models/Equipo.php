<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sala;

class Equipo extends Model
{
    protected $table = 'tbl_equipos';
    public $timestamps = false;
    protected $fillable = ['numero_equipo', 'nombre_equipo', 'id_lider'];

    // numero_equipo stores the sala id — used to scope groups to a sala
    public function integrantes()
    {
        return $this->belongsToMany(Usuario::class, 'tbl_equipo_usuarios', 'id_equipo', 'id_usuario');
    }

    public function lider()
    {
        return $this->belongsTo(Usuario::class, 'id_lider');
    }

    public function sala()
    {
        return $this->belongsTo(Sala::class, 'numero_equipo');
    }

    public function getCodigoInvitacionAttribute(): string
    {
        return str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}
