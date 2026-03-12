<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lugar extends Model
{
    use HasFactory;

    protected $table = 'tbl_lugares';
    
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'direccion_completa',
        'latitud',
        'longitud',
        'id_categoria',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }
}
