<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'tbl_categorias';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'icono_url',
        'color_marcador',
    ];

    public function lugares()
    {
        return $this->hasMany(Lugar::class, 'id_categoria');
    }
}
