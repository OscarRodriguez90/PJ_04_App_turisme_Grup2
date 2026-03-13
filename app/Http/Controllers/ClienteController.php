<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Lugar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        $categorias = Categoria::orderBy('nombre')->get();
        $favoritosIds = $usuario->favoritos()->pluck('tbl_lugares.id')->map(fn ($id) => (int) $id)->values();

        $lugares = Lugar::with('categoria')
            ->orderBy('nombre')
            ->get()
            ->map(function (Lugar $lugar) use ($favoritosIds) {
                return [
                    'id' => $lugar->id,
                    'nombre' => $lugar->nombre,
                    'descripcion' => $lugar->descripcion,
                    'direccion_completa' => $lugar->direccion_completa,
                    'latitud' => (float) $lugar->latitud,
                    'longitud' => (float) $lugar->longitud,
                    'imagen' => $lugar->imagen,
                    'icono' => $lugar->icono,
                    'is_favorito' => $favoritosIds->contains((int) $lugar->id),
                    'categoria' => $lugar->categoria ? [
                        'id' => $lugar->categoria->id,
                        'nombre' => $lugar->categoria->nombre,
                        'color_marcador' => $lugar->categoria->color_marcador,
                        'icono_url' => $lugar->categoria->icono_url,
                    ] : null,
                ];
            })
            ->values();

        return view('cliente.index', [
            'usuario' => $usuario,
            'categorias' => $categorias,
            'favoritosIds' => $favoritosIds,
            'lugares' => $lugares,
        ]);
    }

    public function toggleFavorito(Request $request, Lugar $lugar): JsonResponse
    {
        $usuario = $request->user();
        $yaEsFavorito = $usuario->favoritos()->where('tbl_lugares.id', $lugar->id)->exists();

        if ($yaEsFavorito) {
            $usuario->favoritos()->detach($lugar->id);
        } else {
            $usuario->favoritos()->attach($lugar->id, ['fecha_agregado' => now()]);
        }

        $favoritosIds = $usuario->favoritos()->pluck('tbl_lugares.id')->map(fn ($id) => (int) $id)->values();

        return response()->json([
            'success' => true,
            'is_favorito' => !$yaEsFavorito,
            'favoritos_ids' => $favoritosIds,
            'message' => $yaEsFavorito
                ? 'Lugar eliminado de tus favoritos.'
                : 'Lugar añadido a tus favoritos.',
        ]);
    }
}
