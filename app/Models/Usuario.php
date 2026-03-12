<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'tbl_usuarios';

    public $timestamps = false; // fecha_registro is manual or useCurrent()

    protected $fillable = [
        'username',
        'nombre',
        'apellido1',
        'apellido2',
        'email',
        'password',
        'id_rol',
        'foto',
    ];

    protected $hidden = [
        'password',
    ];
}
