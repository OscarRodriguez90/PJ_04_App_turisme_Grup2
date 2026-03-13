<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'icono',
        'imagen',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function usuariosFavoritos(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'tbl_favoritos', 'id_lugar', 'id_usuario')
            ->withPivot('fecha_agregado');
    }
}
