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
    public function index(): View
    {
        $salas = Sala::where('estado', '!=', 'finalizada')
            ->withCount('equipos')
            ->get();

        return view('sala.index', compact('salas'));
    }

    public function entrar(int $id): RedirectResponse
    {
        $sala = Sala::findOrFail($id);

        if ($sala->estado === 'finalizada') {
            return back()->with('error', 'Esta sala ya ha finalizado.');
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

        $miEquipoId = Equipo::where('numero_equipo', $sala->id)
            ->whereHas('integrantes', fn ($q) => $q->where('tbl_usuarios.id', $usuario->id))
            ->value('id');

        return response()->json([
            'estado' => $sala->estado,
            'miEquipoId' => $miEquipoId ? (int) $miEquipoId : null,
        ]);
    }

    public function crearEquipo(Request $request, int $id): JsonResponse
    {
        $sala = Sala::findOrFail($id);
        $request->validate([
            'nombre_equipo' => 'required|string|min:3|max:50',
        ]);

        $usuario = Auth::user();

        $yaEnEquipo = Equipo::where('numero_equipo', $sala->id)
            ->whereHas('integrantes', fn ($q) => $q->where('tbl_usuarios.id', $usuario->id))
            ->exists();

        if ($yaEnEquipo) {
            return response()->json(['error' => 'Ya estás en un equipo en esta sala.'], 422);
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

        $usuario = Auth::user();

        $yaEnEquipo = Equipo::where('numero_equipo', $sala->id)
            ->whereHas('integrantes', fn ($q) => $q->where('tbl_usuarios.id', $usuario->id))
            ->exists();

        if ($yaEnEquipo) {
            return response()->json(['error' => 'Ya estás en un equipo en esta sala.'], 422);
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
}
