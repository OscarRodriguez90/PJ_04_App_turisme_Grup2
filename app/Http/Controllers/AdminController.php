<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use App\Models\Categoria;
use App\Models\Usuario;
use App\Models\Prueba;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'lugares' => Lugar::count(),
            'categorias' => Categoria::count(),
            'gimcanas' => Prueba::count(), // Assuming Pruebas represent Gimcanas as per user image
            'usuarios' => Usuario::count(),
        ];

        return view('admin.admin_dashboard', compact('stats'));
    }
}
