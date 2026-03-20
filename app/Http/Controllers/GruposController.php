<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GruposController extends Controller
{
    private const MAX_GRUPOS_POR_SALA = 5;
    private const MAX_INTEGRANTES_POR_GRUPO = 8;

    public function index(Request $request): View
    {
        $salaId = $this->resolveSalaId($request);
        $usuario = Auth::user();

        $query = Equipo::whereHas('integrantes', fn ($q) => $q->where('tbl_usuarios.id', $usuario->id));
        $this->applyScopeToEquiposQuery($query, $salaId);
        $miEquipo = $query->with('integrantes')->first();

        $equiposQuery = Equipo::withCount('integrantes')->with('lider')->orderByDesc('id');
        $this->applyScopeToEquiposQuery($equiposQuery, $salaId);
        $equipos = $equiposQuery->get();

        return view('grupos.index', compact('miEquipo', 'equipos', 'salaId'));
    }

    public function store(Request $request): JsonResponse
    {
        $salaId = $this->resolveSalaId($request);
        $request->validate([
            'nombre_equipo' => 'required|string|min:3|max:50',
        ]);

        $usuario = Auth::user();

        $yaEnEquipo = Equipo::whereHas('integrantes', fn ($q) => $q->where('tbl_usuarios.id', $usuario->id));
        $this->applyScopeToEquiposQuery($yaEnEquipo, $salaId);
        if ($yaEnEquipo->exists()) {
            return response()->json(['error' => 'Ya estas en un grupo.'], 422);
        }

        $nombreExistente = Equipo::where('nombre_equipo', trim($request->nombre_equipo));
        $this->applyScopeToEquiposQuery($nombreExistente, $salaId);
        if ($nombreExistente->exists()) {
            return response()->json(['error' => 'Ya existe un grupo con este nombre en esta gimcana.'], 422);
        }

        if ($salaId !== null) {
            $totalGruposSala = Equipo::where('numero_equipo', $salaId)->count();
            if ($totalGruposSala >= self::MAX_GRUPOS_POR_SALA) {
                return response()->json([
                    'error' => 'Esta sala ya tiene el maximo de ' . self::MAX_GRUPOS_POR_SALA . ' grupos.',
                ], 422);
            }
        }

        DB::transaction(function () use ($request, $usuario, $salaId): void {
            $numeroEquipo = $salaId ?? $this->generarCodigoEquipo();
            $equipo = Equipo::create([
                'numero_equipo' => $numeroEquipo,
                'nombre_equipo' => trim($request->nombre_equipo),
                'id_lider' => $usuario->id,
            ]);
            $equipo->integrantes()->attach($usuario->id);
        });

        return response()->json(['success' => true]);
    }

    public function join(Request $request, int $equipo): JsonResponse
    {
        $salaId = $this->resolveSalaId($request);
        $usuario = Auth::user();
        $equipoQuery = Equipo::where('id', $equipo);
        $this->applyScopeToEquiposQuery($equipoQuery, $salaId);
        $equipoModel = $equipoQuery->firstOrFail();

        $yaEnEquipo = Equipo::whereHas('integrantes', fn ($q) => $q->where('tbl_usuarios.id', $usuario->id));
        $this->applyScopeToEquiposQuery($yaEnEquipo, $salaId);
        if ($yaEnEquipo->exists()) {
            return response()->json(['error' => 'Ya estas en un grupo.'], 422);
        }

        if ($equipoModel->integrantes()->count() >= self::MAX_INTEGRANTES_POR_GRUPO) {
            return response()->json([
                'error' => 'Este grupo ya alcanzo el maximo de ' . self::MAX_INTEGRANTES_POR_GRUPO . ' personas.',
            ], 422);
        }

        $equipoModel->integrantes()->syncWithoutDetaching([$usuario->id]);
        return response()->json(['success' => true]);
    }

    public function leave(Request $request): JsonResponse
    {
        $usuario = Auth::user();
        $salaId = $this->resolveSalaId($request);

        $query = Equipo::whereHas('integrantes', fn ($q) => $q->where('tbl_usuarios.id', $usuario->id));
        $this->applyScopeToEquiposQuery($query, $salaId);
        $equipo = $query->first();

        if ($equipo) {
            DB::transaction(function () use ($equipo, $usuario): void {
                $pivotId = DB::table('tbl_equipo_usuarios')
                    ->where('id_equipo', $equipo->id)
                    ->where('id_usuario', $usuario->id)
                    ->value('id');

                if (!$pivotId) {
                    return;
                }

                // Remove challenge progress linked to this membership before deleting the pivot row.
                DB::table('tbl_progreso_retos')
                    ->where('id_equipo_usuario', $pivotId)
                    ->delete();

                $equipo->integrantes()->detach($usuario->id);

                if ($equipo->integrantes()->count() === 0) {
                    $equipo->delete();
                    return;
                }

                if ((int) $equipo->id_lider === (int) $usuario->id) {
                    $nuevoLiderId = DB::table('tbl_equipo_usuarios')
                        ->where('id_equipo', $equipo->id)
                        ->orderBy('id')
                        ->value('id_usuario');

                    if ($nuevoLiderId) {
                        $equipo->id_lider = $nuevoLiderId;
                        $equipo->save();
                    }
                }
            });
        }

        return response()->json(['success' => true]);
    }

    public function joinByCode(Request $request): JsonResponse
    {
        $salaId = $this->resolveSalaId($request);
        $request->validate([
            'codigo' => 'required|digits:6',
        ]);

        $usuario = Auth::user();
        $codigo = (int) trim($request->codigo);

        $equipoQuery = Equipo::where('id', $codigo);
        $this->applyScopeToEquiposQuery($equipoQuery, $salaId);
        $equipoModel = $equipoQuery->first();

        if (!$equipoModel) {
            return response()->json(['error' => 'Código de grupo no válido.'], 422);
        }

        $yaEnEquipo = Equipo::whereHas('integrantes', fn ($q) => $q->where('tbl_usuarios.id', $usuario->id));
        $this->applyScopeToEquiposQuery($yaEnEquipo, $salaId);
        if ($yaEnEquipo->exists()) {
            return response()->json(['error' => 'Ya estás en un grupo.'], 422);
        }

        if ($equipoModel->integrantes()->count() >= self::MAX_INTEGRANTES_POR_GRUPO) {
            return response()->json([
                'error' => 'Este grupo ya alcanzo el maximo de ' . self::MAX_INTEGRANTES_POR_GRUPO . ' personas.',
            ], 422);
        }

        $equipoModel->integrantes()->syncWithoutDetaching([$usuario->id]);
        return response()->json(['success' => true]);
    }

    private function generarCodigoEquipo(): int
    {
        do {
            $codigo = random_int(100000, 999999);
        } while (Equipo::where('numero_equipo', $codigo)->exists());

        return $codigo;
    }

    private function resolveSalaId(Request $request): ?int
    {
        $routeSalaId = $request->route('id');
        if ($routeSalaId !== null && $routeSalaId !== '') {
            return (int) $routeSalaId;
        }

        $payloadSalaId = $request->input('salaId');
        if ($payloadSalaId !== null && $payloadSalaId !== '') {
            return (int) $payloadSalaId;
        }

        return null;
    }

    private function applyScopeToEquiposQuery($query, ?int $salaId): void
    {
        if ($salaId !== null) {
            $query->where('numero_equipo', $salaId);
            return;
        }

        $query->where('numero_equipo', '>=', 100000);
    }
}
