<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Prueba;
use App\Models\Sala;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GimcanaController extends Controller
{
    public function mapa(Request $request): View|RedirectResponse
    {
        $context = $this->resolveContext($request);
        if ($context instanceof RedirectResponse) {
            return $context;
        }

        ['equipo' => $equipo, 'sala' => $sala, 'pivotId' => $pivotId] = $context;

        $retoActual = $this->getRetoActual($sala->id, $pivotId);
        if (!$retoActual) {
            return redirect()->route('gimcana.final')->with('success', 'Todos los retos completados.');
        }

        return view('gimcana.mapa', [
            'equipo' => $equipo,
            'sala' => $sala,
            'retoActual' => $retoActual,
        ]);
    }

    public function pregunta(Request $request, ?int $reto = null): View|RedirectResponse
    {
        $context = $this->resolveContext($request);
        if ($context instanceof RedirectResponse) {
            return $context;
        }

        ['equipo' => $equipo, 'sala' => $sala, 'pivotId' => $pivotId] = $context;

        $retoActual = $this->resolveReto($sala->id, $pivotId, $reto);
        if (!$retoActual) {
            return redirect()->route('gimcana.final')->with('success', 'No hay mas retos pendientes.');
        }

        $integrantesIds = $equipo->integrantes->pluck('id')->all();
        $integrantesEnReto = DB::table('tbl_progreso_retos as pr')
            ->join('tbl_equipo_usuarios as eu', 'eu.id', '=', 'pr.id_equipo_usuario')
            ->where('pr.id_reto', $retoActual->id)
            ->where('pr.completado', true)
            ->whereIn('eu.id_usuario', $integrantesIds)
            ->where('eu.id_equipo', $equipo->id)
            ->count();

        return view('gimcana.pregunta', [
            'equipo' => $equipo,
            'sala' => $sala,
            'retoActual' => $retoActual,
            'integrantesEnReto' => $integrantesEnReto,
        ]);
    }

    public function resolverPregunta(Request $request, int $reto): RedirectResponse
    {
        $context = $this->resolveContext($request);
        if ($context instanceof RedirectResponse) {
            return $context;
        }

        ['sala' => $sala, 'pivotId' => $pivotId] = $context;

        $request->validate([
            'respuesta' => 'required|string|max:255',
        ], [
            'respuesta.required' => 'Debes introducir una respuesta.',
        ]);

        $retoActual = Prueba::where('id', $reto)
            ->where('id_sala', $sala->id)
            ->firstOrFail();

        if (!$this->answersMatch((string) $request->respuesta, (string) $retoActual->respuesta_correcta)) {
            return back()->withErrors([
                'respuesta' => 'Respuesta incorrecta. Intentalo de nuevo.',
            ])->withInput();
        }

        DB::table('tbl_progreso_retos')->updateOrInsert(
            [
                'id_equipo_usuario' => $pivotId,
                'id_reto' => $retoActual->id,
            ],
            [
                'completado' => true,
                'fecha_completado' => now(),
            ]
        );

        $siguiente = $this->getRetoActual($sala->id, $pivotId);
        if ($siguiente) {
            return redirect()->route('gimcana.mapa')->with('success', 'Reto completado. Siguiente destino desbloqueado.');
        }

        return redirect()->route('gimcana.final')->with('success', 'Has completado toda la gimcana.');
    }

    public function final(Request $request): View|RedirectResponse
    {
        $context = $this->resolveContext($request);
        if ($context instanceof RedirectResponse) {
            return $context;
        }

        ['equipo' => $equipo, 'sala' => $sala, 'pivotId' => $pivotId] = $context;

        $retoPendiente = $this->getRetoActual($sala->id, $pivotId);
        if ($retoPendiente) {
            return redirect()->route('gimcana.mapa');
        }

        $retosTotales = Prueba::where('id_sala', $sala->id)->count();
        $retosCompletados = DB::table('tbl_progreso_retos')
            ->where('id_equipo_usuario', $pivotId)
            ->where('completado', true)
            ->count();

        return view('gimcana.final', [
            'equipo' => $equipo,
            'sala' => $sala,
            'retosTotales' => $retosTotales,
            'retosCompletados' => $retosCompletados,
        ]);
    }

    public function reiniciar(Request $request): RedirectResponse
    {
        $context = $this->resolveContext($request);
        if ($context instanceof RedirectResponse) {
            return $context;
        }

        ['sala' => $sala, 'pivotId' => $pivotId] = $context;

        DB::table('tbl_progreso_retos')
            ->where('id_equipo_usuario', $pivotId)
            ->delete();

        return redirect()->route('sala.show', $sala->id)
            ->with('success', 'Progreso reiniciado. Ya podeis empezar los retos de nuevo.');
    }

    public function progreso(Request $request): View|RedirectResponse
    {
        $context = $this->resolveContext($request);
        if ($context instanceof RedirectResponse) {
            return $context;
        }

        ['sala' => $sala, 'pivotId' => $pivotId] = $context;

        $retos = Prueba::with('lugar')
            ->where('id_sala', $sala->id)
            ->orderBy('orden')
            ->get();

        $completadosIds = DB::table('tbl_progreso_retos')
            ->where('id_equipo_usuario', $pivotId)
            ->where('completado', true)
            ->pluck('id_reto')
            ->map(fn ($id) => (int) $id)
            ->all();

        $ordenActual = $retos
            ->first(fn (Prueba $r) => !in_array((int) $r->id, $completadosIds, true))
            ?->orden;

        return view('gimcana.progreso', [
            'sala' => $sala,
            'retos' => $retos,
            'completadosIds' => $completadosIds,
            'ordenActual' => $ordenActual,
        ]);
    }

    private function resolveContext(Request $request): array|RedirectResponse
    {
        $usuario = $request->user();

        $equipo = Equipo::whereHas('integrantes', fn ($q) => $q->where('tbl_usuarios.id', $usuario->id))
            ->whereIn('numero_equipo', Sala::query()->select('id'))
            ->with('integrantes')
            ->first();

        if (!$equipo) {
            return redirect()->route('sala.index')->with('error', 'Debes unirte a un equipo dentro de una sala para empezar retos.');
        }

        $sala = Sala::find($equipo->numero_equipo);
        if (!$sala) {
            return redirect()->route('sala.index')->with('error', 'Sala no valida para este equipo.');
        }

        $pivotId = DB::table('tbl_equipo_usuarios')
            ->where('id_equipo', $equipo->id)
            ->where('id_usuario', $usuario->id)
            ->value('id');

        if (!$pivotId) {
            return redirect()->route('sala.show', $sala->id)->with('error', 'No se encontro la vinculacion del usuario con el equipo.');
        }

        return [
            'equipo' => $equipo,
            'sala' => $sala,
            'pivotId' => (int) $pivotId,
        ];
    }

    private function getRetoActual(int $salaId, int $pivotId): ?Prueba
    {
        $completados = DB::table('tbl_progreso_retos')
            ->where('id_equipo_usuario', $pivotId)
            ->where('completado', true)
            ->pluck('id_reto');

        return Prueba::with('lugar')
            ->where('id_sala', $salaId)
            ->when($completados->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $completados))
            ->orderBy('orden')
            ->first();
    }

    private function resolveReto(int $salaId, int $pivotId, ?int $reto): ?Prueba
    {
        if ($reto === null) {
            return $this->getRetoActual($salaId, $pivotId);
        }

        return Prueba::with('lugar')
            ->where('id', $reto)
            ->where('id_sala', $salaId)
            ->first();
    }

    private function answersMatch(string $input, string $correct): bool
    {
        $normalize = static function (string $value): string {
            $value = mb_strtolower(trim($value), 'UTF-8');
            $value = preg_replace('/\s+/u', ' ', $value) ?? $value;
            return $value;
        };

        return $normalize($input) === $normalize($correct);
    }
}
