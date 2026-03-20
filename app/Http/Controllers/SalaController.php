<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Sala;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalaController extends Controller
{
    private const MAX_GRUPOS_POR_SALA = 5;
    private const MAX_INTEGRANTES_POR_EQUIPO = 8;

    public function index(): View
    {
        $salas = Sala::where('estado', '!=', 'finalizada')
            ->withCount('equipos')
            ->get();

        return view('sala.index', compact('salas'));
    }

    public function entrar(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $sala = Sala::findOrFail($id);

        if ($sala->estado === 'finalizada') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Esta sala ya ha finalizado.'], 403);
            }
            return back()->with('error', 'Esta sala ya ha finalizado.');
        }

        if ($sala->estado === 'jugando') {
            $usuario = Auth::user();
            $yaEnEquipo = Equipo::where('numero_equipo', $sala->id)
                ->whereHas('integrantes', fn ($q) => $q->where('tbl_usuarios.id', $usuario->id))
                ->exists();
            if (!$yaEnEquipo) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Esta gimcana está en curso. No puedes unirte ahora.'], 403);
                }
                return back()->with('error', 'Esta gimcana está en curso. No puedes unirte ahora.');
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'redirect' => route('sala.show', $sala->id)]);
        }

        return redirect()->route('sala.show', $sala->id);
    }

    public function show(int $id): View
    {
        $sala = Sala::findOrFail($id);
        $usuario = Auth::user();
        $retosCompletados = false;

        $miEquipo = Equipo::where('numero_equipo', $sala->id)
            ->whereHas('integrantes', fn ($q) => $q->where('tbl_usuarios.id', $usuario->id))
            ->with('integrantes')
            ->first();

        $equipos = Equipo::where('numero_equipo', $sala->id)
            ->withCount('integrantes')
            ->with('lider')
            ->get();

        if ($miEquipo) {
            $pivotId = DB::table('tbl_equipo_usuarios')
                ->where('id_equipo', $miEquipo->id)
                ->where('id_usuario', $usuario->id)
                ->value('id');

            if ($pivotId) {
                $totalRetos = DB::table('tbl_retos')
                    ->where('id_sala', $sala->id)
                    ->count();

                $completados = DB::table('tbl_progreso_retos')
                    ->where('id_equipo_usuario', $pivotId)
                    ->where('completado', true)
                    ->count();

                $retosCompletados = $totalRetos > 0 && $completados >= $totalRetos;
            }
        }

        return view('sala.show', compact('sala', 'miEquipo', 'equipos', 'retosCompletados'));
    }

    public function estadoLive(Request $request, int $id): JsonResponse
    {
        $sala = Sala::findOrFail($id);
        $usuario = Auth::user();

        // Equipos de la sala
        $equipos = Equipo::where('numero_equipo', $sala->id)
            ->withCount('integrantes')
            ->with(['lider', 'integrantes'])
            ->get();

        $miEquipo = $equipos->first(function($eq) use ($usuario) {
            return $eq->integrantes->contains('id', $usuario->id);
        });

        // Formatear equipos para el JSON
        $equiposFormatted = $equipos->map(function($eq) use ($miEquipo) {
            return [
                'id' => $eq->id,
                'nombre_equipo' => $eq->nombre_equipo,
                'integrantes_count' => $eq->integrantes_count,
                'lider_nombre' => $eq->lider->nombre ?? '–',
                'es_mio' => $miEquipo && $miEquipo->id === $eq->id,
            ];
        });

        // Formatear mi equipo
        $miEquipoFormatted = null;
        if ($miEquipo) {
            $miEquipoFormatted = [
                'id' => $miEquipo->id,
                'nombre_equipo' => $miEquipo->nombre_equipo,
                'id_lider' => $miEquipo->id_lider,
                'integrantes' => $miEquipo->integrantes->map(function($user) {
                    return [
                        'id' => $user->id,
                        'nombre' => $user->nombre,
                        'foto' => !empty($user->foto) ? asset('img/usuarios/' . $user->foto) : asset('img/usuarios/default_user.png'),
                    ];
                }),
            ];
        }

        return response()->json([
            'nombre' => $sala->nombre,
            'estado' => $sala->estado,
            'miEquipoId' => $miEquipo ? $miEquipo->id : null,
            'miEquipo' => $miEquipoFormatted,
            'equipos' => $equiposFormatted,
            'usuarioId' => $usuario->id,
            'usuarioNombre' => $usuario->nombre,
        ]);
    }

    public function crearEquipo(Request $request, int $id): JsonResponse
    {
        $sala = Sala::findOrFail($id);
        $request->validate([
            'nombre_equipo' => 'required|string|min:3|max:50',
        ]);

        $usuario = Auth::user();

        if ($sala->estado === 'jugando') {
            return response()->json(['error' => 'No puedes crear un equipo mientras la gimcana está en curso.'], 403);
        }

        $yaEnEquipo = Equipo::where('numero_equipo', $sala->id)
            ->whereHas('integrantes', fn ($q) => $q->where('tbl_usuarios.id', $usuario->id))
            ->exists();

        if ($yaEnEquipo) {
            return response()->json(['error' => 'Ya estás en un equipo en esta sala.'], 422);
        }

        $nombreExistente = Equipo::where('numero_equipo', $sala->id)
            ->where('nombre_equipo', $request->nombre_equipo)
            ->exists();

        if ($nombreExistente) {
            return response()->json(['error' => 'Ya existe un grupo con este nombre en esta sala.'], 422);
        }

        $totalEquiposSala = Equipo::where('numero_equipo', $sala->id)->count();
        if ($totalEquiposSala >= self::MAX_GRUPOS_POR_SALA) {
            return response()->json([
                'error' => 'Esta sala ya tiene el máximo de ' . self::MAX_GRUPOS_POR_SALA . ' grupos.',
            ], 422);
        }

        DB::transaction(function () use ($request, $sala, $usuario) {
            $equipo = Equipo::create([
                'numero_equipo' => $sala->id,
                'nombre_equipo' => $request->nombre_equipo,
                'id_lider'      => $usuario->id,
            ]);
            $equipo->integrantes()->attach($usuario->id);
        });

        return response()->json(['success' => true]);
    }

    public function unirse(Request $request, int $id, int $equipo): JsonResponse
    {
        $sala = Sala::findOrFail($id);
        $equipoModel = Equipo::where('id', $equipo)
            ->where('numero_equipo', $sala->id)
            ->firstOrFail();

        if ($sala->estado === 'jugando') {
            return response()->json(['error' => 'No puedes unirte a un equipo mientras la gimcana está en curso.'], 403);
        }

        $usuario = Auth::user();

        $yaEnEquipo = Equipo::where('numero_equipo', $sala->id)
            ->whereHas('integrantes', fn ($q) => $q->where('tbl_usuarios.id', $usuario->id))
            ->exists();

        if ($yaEnEquipo) {
            return response()->json(['error' => 'Ya estás en un equipo en esta sala.'], 422);
        }

        $integrantesCount = $equipoModel->integrantes()->count();
        if ($integrantesCount >= self::MAX_INTEGRANTES_POR_EQUIPO) {
            return response()->json([
                'error' => 'Este grupo ya alcanzó el máximo de ' . self::MAX_INTEGRANTES_POR_EQUIPO . ' personas.',
            ], 422);
        }

        $equipoModel->integrantes()->syncWithoutDetaching([$usuario->id]);

        return response()->json(['success' => true]);
    }

    public function salirEquipo(Request $request, int $id): JsonResponse
    {
        $sala = Sala::findOrFail($id);
        $usuario = Auth::user();

        $equipo = Equipo::where('numero_equipo', $sala->id)
            ->whereHas('integrantes', fn ($q) => $q->where('tbl_usuarios.id', $usuario->id))
            ->first();

        if ($equipo) {
            DB::transaction(function () use ($equipo, $usuario): void {
                $pivotId = DB::table('tbl_equipo_usuarios')
                    ->where('id_equipo', $equipo->id)
                    ->where('id_usuario', $usuario->id)
                    ->value('id');

                if (!$pivotId) {
                    return;
                }

                // Clean up challenge progress tied to this team-user membership before detaching it.
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

    public function historial(): View
    {
        $usuario = Auth::user();

        $rows = DB::table('tbl_historial')
            ->where('id_usuario', $usuario->id)
            ->orderByDesc('created_at')
            ->get();

        $historial = $rows->map(function ($row) {
            $sala = Sala::find($row->id_sala);
            $equipo = Equipo::with('integrantes')->find($row->id_equipo);

            return [
                'sala'         => $sala,
                'equipo'       => $equipo,
                'resultado'    => $row->resultado,
                'retos'        => json_decode($row->retos_completados, true),
                'totalSeconds' => $row->tiempo_total_segundos,
                'fecha'        => $row->fecha_fin ?? $row->created_at,
            ];
        })->filter(fn ($e) => $e['sala'] && $e['equipo'])->values();

        return view('sala.historial', [
            'historial' => $historial,
            'usuario'   => $usuario,
        ]);
    }
}
