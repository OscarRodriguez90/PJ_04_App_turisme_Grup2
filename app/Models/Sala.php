<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sala extends Model
{
    use HasFactory;

    protected $table = 'tbl_salas';
    
    // Si la tabla no usa created_at o updated_at, de momento tbl_salas solo tiene fecha_creacion
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_creacion'
    ];

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'id_creador');
    }

    public function pruebas()
    {
        return $this->hasMany(Prueba::class, 'id_sala');
    }
}
