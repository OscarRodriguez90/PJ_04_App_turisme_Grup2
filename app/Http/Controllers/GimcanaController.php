<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Lugar;
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

        ['equipo' => $equipo, 'sala' => $sala, 'pivotId' => $pivotId, 'usuario' => $usuario] = $context;
        $state = $this->buildTeamProgressState($equipo, $sala, $pivotId);

        if ($state['winner'] && $state['winner']['id'] !== $equipo->id) {
            return redirect()->route('gimcana.derrota');
        }

        if ($state['allTeamRetosCompleted']) {
            return redirect()->route('gimcana.final')->with('success', 'Todos los retos completados.');
        }

        if ($state['userWaiting']) {
            return redirect()->route('gimcana.espera');
        }

        $retoActual = $state['currentReto'];
        if (!$retoActual) {
            return redirect()->route('gimcana.final');
        }

        $lugares = Lugar::with('categoria')->get();

        return view('gimcana.mapa', [
            'equipo'     => $equipo,
            'usuario'    => $request->user(),
            'sala'       => $sala,
            'retoActual' => $retoActual,
            'avatarUrl' => $this->avatarUrl($usuario),
            'lugares'    => $lugares,
        ]);
    }

    public function pregunta(Request $request, ?int $reto = null): View|RedirectResponse
    {
        $context = $this->resolveContext($request);
        if ($context instanceof RedirectResponse) {
            return $context;
        }

        ['equipo' => $equipo, 'sala' => $sala, 'pivotId' => $pivotId, 'usuario' => $usuario] = $context;
        $state = $this->buildTeamProgressState($equipo, $sala, $pivotId);

        if ($state['winner'] && $state['winner']['id'] !== $equipo->id) {
            return redirect()->route('gimcana.derrota');
        }

        if ($state['allTeamRetosCompleted']) {
            return redirect()->route('gimcana.final')->with('success', 'No hay mas retos pendientes.');
        }

        if ($state['userWaiting']) {
            return redirect()->route('gimcana.espera');
        }

        $retoActual = $state['currentReto'];
        if (!$retoActual) {
            return redirect()->route('gimcana.final');
        }

        if ($reto !== null && (int) $reto !== (int) $retoActual->id) {
            return redirect()->route('gimcana.mapa');
        }

        $integrantesEnReto = $state['integrantesCompletadosCurrent'];

        return view('gimcana.pregunta', [
            'equipo' => $equipo,
            'sala' => $sala,
            'retoActual' => $retoActual,
            'integrantesEnReto' => $integrantesEnReto,
            'avatarUrl' => $this->avatarUrl($usuario),
        ]);
    }

    public function resolverPregunta(Request $request, int $reto): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $context = $this->resolveContext($request);
        if ($context instanceof RedirectResponse) {
            return $context;
        }

        ['equipo' => $equipo, 'sala' => $sala, 'pivotId' => $pivotId] = $context;

        $stateBefore = $this->buildTeamProgressState($equipo, $sala, $pivotId);
        $retoActual = $stateBefore['currentReto'];

        if (!$retoActual || (int) $reto !== (int) $retoActual->id) {
            return redirect()->route('gimcana.mapa');
        }

        $request->validate([
            'respuesta' => 'required|string|max:255',
        ], [
            'respuesta.required' => 'Debes introducir una respuesta.',
        ]);

        $retoModel = Prueba::where('id', $reto)
            ->where('id_sala', $sala->id)
            ->firstOrFail();

        if (!$this->answersMatch((string) $request->respuesta, (string) $retoModel->respuesta_correcta)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => ['respuesta' => 'Respuesta incorrecta. Inténtalo de nuevo.']
                ], 422);
            }
            return back()->withErrors([
                'respuesta' => 'Respuesta incorrecta. Intentalo de nuevo.',
            ])->withInput();
        }

        DB::table('tbl_progreso_retos')->updateOrInsert(
            [
                'id_equipo_usuario' => $pivotId,
                'id_reto' => $retoModel->id,
            ],
            [
                'completado' => true,
                'fecha_completado' => now(),
            ]
        );

        $stateAfter = $this->buildTeamProgressState($equipo, $sala, $pivotId);

        if ($stateAfter['allTeamRetosCompleted'] && $sala->estado !== 'finalizada') {
            $sala->estado = 'finalizada';
            $sala->fecha_fin = now();
            $sala->save();

            // Snapshot the entire game into tbl_historial for all teams
            $this->snapshotHistorial($sala);
        }

        if ($request->expectsJson()) {
            $redirect = route('gimcana.mapa');
            if ($stateAfter['allTeamRetosCompleted']) {
                $redirect = route('gimcana.final');
            } elseif ($stateAfter['userWaiting']) {
                $redirect = route('gimcana.espera');
            }

            return response()->json([
                'success' => true,
                'message' => 'Reto completado.',
                'redirect' => $redirect
            ]);
        }

        if ($stateAfter['allTeamRetosCompleted']) {
            return redirect()->route('gimcana.final')->with('success', 'Has completado toda la gimcana.');
        }

        if ($stateAfter['userWaiting']) {
            return redirect()->route('gimcana.espera')->with('success', 'Respuesta correcta. Esperando a tu equipo...');
        }

        return redirect()->route('gimcana.mapa')->with('success', 'Reto completado. Siguiente destino desbloqueado.');
    }

    public function espera(Request $request): View|RedirectResponse
    {
        $context = $this->resolveContext($request);
        if ($context instanceof RedirectResponse) {
            return $context;
        }

        ['equipo' => $equipo, 'sala' => $sala, 'pivotId' => $pivotId, 'usuario' => $usuario] = $context;
        $state = $this->buildTeamProgressState($equipo, $sala, $pivotId);

        if ($state['winner'] && $state['winner']['id'] !== $equipo->id) {
            return redirect()->route('gimcana.derrota');
        }

        if ($state['allTeamRetosCompleted']) {
            return redirect()->route('gimcana.final');
        }

        if (!$state['userWaiting']) {
            return redirect()->route('gimcana.mapa');
        }

        return view('gimcana.espera', [
            'equipo' => $equipo,
            'sala' => $sala,
            'retoActual' => $state['currentReto'],
            'integrantesCompletados' => $state['integrantesCompletadosCurrent'],
            'totalIntegrantes' => $state['teamMemberCount'],
            'miembrosPendientes' => max(0, $state['teamMemberCount'] - $state['integrantesCompletadosCurrent']),
            'avatarUrl' => $this->avatarUrl($usuario),
            'integrantesPendientes' => $state['integrantesPendientes'],
        ]);
    }

    public function final(Request $request): View|RedirectResponse
    {
        $context = $this->resolveContext($request);
        if ($context instanceof RedirectResponse) {
            return $context;
        }

        ['equipo' => $equipo, 'sala' => $sala, 'pivotId' => $pivotId, 'usuario' => $usuario] = $context;
        $state = $this->buildTeamProgressState($equipo, $sala, $pivotId);

        if (!$state['allTeamRetosCompleted']) {
            if (isset($state['winner']) && $state['winner']) {
                return redirect()->route('gimcana.derrota');
            }
            return $state['userWaiting']
                ? redirect()->route('gimcana.espera')
                : redirect()->route('gimcana.mapa');
        }

        $teamPivotIds = DB::table('tbl_equipo_usuarios')
            ->where('id_equipo', $equipo->id)
            ->pluck('id')
            ->all();

        $stats = $this->buildGameStats($equipo, $sala, $teamPivotIds);

        return view('gimcana.final', [
            'equipo' => $equipo,
            'sala' => $sala,
            'stats' => $stats,
            'avatarUrl' => $this->avatarUrl($usuario),
        ]);
    }

    public function derrota(Request $request): View|RedirectResponse
    {
        $context = $this->resolveContext($request);
        if ($context instanceof RedirectResponse) {
            return $context;
        }

        ['equipo' => $equipo, 'sala' => $sala, 'pivotId' => $pivotId, 'usuario' => $usuario] = $context;
        $state = $this->buildTeamProgressState($equipo, $sala, $pivotId);

        if (!$state['winner']) {
            return redirect()->route('gimcana.mapa');
        }

        if ($state['winner']['id'] === $equipo->id) {
            return redirect()->route('gimcana.final');
        }

        $teamPivotIds = DB::table('tbl_equipo_usuarios')
            ->where('id_equipo', $equipo->id)
            ->pluck('id')
            ->all();

        $stats = $this->buildGameStats($equipo, $sala, $teamPivotIds);

        return view('gimcana.derrota', [
            'equipo' => $equipo,
            'sala' => $sala,
            'winner' => $state['winner'],
            'stats' => $stats,
            'avatarUrl' => $this->avatarUrl($usuario),
        ]);
    }

    public function actualizarUbicacion(Request $request): \Illuminate\Http\JsonResponse
    {
        $context = $this->resolveContext($request);
        if ($context instanceof RedirectResponse) {
            return response()->json(['error' => 'No context'], 403);
        }

        ['equipo' => $equipo, 'sala' => $sala, 'pivotId' => $pivotId] = $context;
        $state = $this->buildTeamProgressState($equipo, $sala, $pivotId);

        $gameOver = false;
        $redirectUrl = null;

        if ($state['winner']) {
            $gameOver = true;
            $redirectUrl = ($state['winner']['id'] === $equipo->id) 
                ? route('gimcana.final') 
                : route('gimcana.derrota');
        }

        return response()->json([
            'gameOver' => $gameOver,
            'redirectUrl' => $redirectUrl,
            'userWaiting' => $state['userWaiting'],
            'integrantesPendientes' => $state['integrantesPendientes'],
        ]);
    }

    private function buildGameStats(Equipo $equipo, Sala $sala, array $teamPivotIds): array
    {
        $retos = Prueba::with('lugar')
            ->where('id_sala', $sala->id)
            ->orderBy('orden')
            ->get();

        $statsByReto = [];
        $startTime = $sala->fecha_inicio ? \Carbon\Carbon::parse($sala->fecha_inicio) : \Carbon\Carbon::parse($sala->created_at);
        $lastRetoTime = $startTime;

        foreach ($retos as $index => $reto) {
            $maxFecha = DB::table('tbl_progreso_retos')
                ->where('id_reto', $reto->id)
                ->whereIn('id_equipo_usuario', $teamPivotIds)
                ->where('completado', true)
                ->max('fecha_completado');

            if ($maxFecha) {
                $maxFecha = \Carbon\Carbon::parse($maxFecha);
                $durationInSeconds = $lastRetoTime ? abs($maxFecha->diffInSeconds($lastRetoTime)) : 0;
                
                $statsByReto[] = [
                    'orden' => $reto->orden,
                    'lugar' => $reto->lugar->nombre,
                    'finalizado_at' => $maxFecha->format('H:i:s'),
                    'duracion' => $this->formatDuration($durationInSeconds),
                    'segundos' => $durationInSeconds,
                ];
                $lastRetoTime = $maxFecha;
            } else {
                $statsByReto[] = [
                    'orden' => $reto->orden,
                    'lugar' => $reto->lugar->nombre,
                    'finalizado_at' => '-',
                    'duracion' => 'Pendiente',
                    'segundos' => 0,
                ];
            }
        }

        $totalSeconds = 0;
        if ($startTime && $lastRetoTime) {
            $totalSeconds = abs($lastRetoTime->diffInSeconds($startTime));
        }

        return [
            'totalTime' => $this->formatDuration($totalSeconds),
            'totalSeconds' => $totalSeconds,
            'retos' => $statsByReto,
        ];
    }

    private function formatDuration(int $seconds): string
    {
        if ($seconds <= 0) return '0s';
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = $seconds % 60;

        $parts = [];
        if ($h > 0) $parts[] = $h . 'h';
        if ($m > 0) $parts[] = $m . 'm';
        if ($s > 0 || empty($parts)) $parts[] = $s . 's';

        return implode(' ', $parts);
    }

    public function reiniciar(Request $request): RedirectResponse
    {
        $context = $this->resolveContext($request);
        if ($context instanceof RedirectResponse) {
            return $context;
        }

        ['equipo' => $equipo, 'sala' => $sala] = $context;

        $teamPivotIds = DB::table('tbl_equipo_usuarios')
            ->where('id_equipo', $equipo->id)
            ->pluck('id')
            ->all();

        if (!empty($teamPivotIds)) {
            DB::table('tbl_progreso_retos')
                ->whereIn('id_equipo_usuario', $teamPivotIds)
                ->delete();
        }

        return redirect()->route('sala.show', $sala->id)
            ->with('success', 'Progreso del equipo reiniciado. Ya podeis empezar los retos de nuevo.');
    }

    public function progreso(Request $request): View|RedirectResponse
    {
        $context = $this->resolveContext($request);
        if ($context instanceof RedirectResponse) {
            return $context;
        }

        ['equipo' => $equipo, 'sala' => $sala, 'pivotId' => $pivotId, 'usuario' => $usuario] = $context;

        $retos = Prueba::with('lugar')
            ->where('id_sala', $sala->id)
            ->orderBy('orden')
            ->get();

        $state = $this->buildTeamProgressState($equipo, $sala, $pivotId);

        if ($state['winner'] && $state['winner']['id'] !== $equipo->id) {
            return redirect()->route('gimcana.derrota');
        }

        $completadosIds = $state['teamCompletedRetoIds'];
        $ordenActual = $state['currentReto']?->orden;
        $integrantesPendientes = $state['integrantesPendientes'];

        $userWaiting = $state['userWaiting'];

        return view('gimcana.progreso', [
            'sala' => $sala,
            'usuario' => $request->user(),
            'retos' => $retos,
            'completadosIds' => $completadosIds,
            'ordenActual' => $ordenActual,
            'avatarUrl' => $this->avatarUrl($usuario),
            'integrantesPendientes' => $integrantesPendientes,
            'userWaiting' => $userWaiting,
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

        if ($sala->estado === 'esperando') {
            return redirect()->route('sala.show', $sala->id)
                ->with('success', 'La partida ha sido reiniciada por el administrador. Volved a entrar cuando os avisen.');
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
            'usuario' => $usuario,
        ];
    }

    private function avatarUrl($usuario): string
    {
        return !empty($usuario->foto)
            ? asset('img/usuarios/' . $usuario->foto)
            : asset('img/usuarios/default_user.png');
    }

    private function buildTeamProgressState(Equipo $equipo, Sala $sala, int $pivotId): array
    {
        $teamPivotIds = DB::table('tbl_equipo_usuarios')
            ->where('id_equipo', $equipo->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $teamMemberCount = count($teamPivotIds);

        $retos = Prueba::with('lugar')
            ->where('id_sala', $sala->id)
            ->orderBy('orden')
            ->get();

        if ($teamMemberCount === 0 || $retos->isEmpty()) {
            return [
                'teamCompletedRetoIds' => [],
                'currentReto' => null,
                'teamMemberCount' => $teamMemberCount,
                'integrantesCompletadosCurrent' => 0,
                'userCompletedCurrent' => false,
                'userWaiting' => false,
                'allTeamRetosCompleted' => false,
            ];
        }

        $completionCounts = DB::table('tbl_progreso_retos')
            ->select('id_reto', DB::raw('COUNT(*) as completados'))
            ->whereIn('id_equipo_usuario', $teamPivotIds)
            ->where('completado', true)
            ->groupBy('id_reto')
            ->pluck('completados', 'id_reto');

        $teamCompletedRetoIds = [];
        foreach ($retos as $reto) {
            $completedForReto = (int) ($completionCounts[$reto->id] ?? 0);
            if ($completedForReto >= $teamMemberCount) {
                $teamCompletedRetoIds[] = (int) $reto->id;
            }
        }

        $currentReto = $retos->first(fn (Prueba $reto) => !in_array((int) $reto->id, $teamCompletedRetoIds, true));

        $integrantesCompletadosCurrent = 0;
        $userCompletedCurrent = false;
        $userWaiting = false;

        if ($currentReto) {
            $integrantesCompletadosCurrent = (int) ($completionCounts[$currentReto->id] ?? 0);
            $userCompletedCurrent = DB::table('tbl_progreso_retos')
                ->where('id_equipo_usuario', $pivotId)
                ->where('id_reto', $currentReto->id)
                ->where('completado', true)
                ->exists();

            $userWaiting = $userCompletedCurrent && $integrantesCompletadosCurrent < $teamMemberCount;
        }

        $allTeamRetosCompleted = $retos->count() > 0 && count($teamCompletedRetoIds) >= $retos->count();

        // --- DETECTAR GANADOR GLOBAL ---
        $winnerInfo = null;

        // Optimización: Si la sala ya está finalizada, buscar directamente en el historial
        if ($sala->estado === 'finalizada') {
            $winnerRow = DB::table('tbl_historial')
                ->where('id_sala', $sala->id)
                ->where('resultado', 'victoria')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($winnerRow && is_object($winnerRow)) {
                $winnerEq = Equipo::with('integrantes')->find($winnerRow->id_equipo);
                $winnerInfo = [
                    'id' => $winnerRow->id_equipo,
                    'nombre' => $winnerEq ? $winnerEq->nombre_equipo : 'Equipo ganador',
                    'jugadores' => $winnerEq ? $winnerEq->integrantes->pluck('nombre')->implode(', ') : '',
                ];
            }
        }

        // Si no hay ganador en historial (o la sala no está finalizada aún), calcular en vivo
        if (!$winnerInfo) {
            $equiposSala = $sala->equipos()->with('integrantes')->get();
            foreach ($equiposSala as $eq) {
                $eqPivotIds = DB::table('tbl_equipo_usuarios')
                    ->where('id_equipo', $eq->id)
                    ->pluck('id')
                    ->all();
                
                if (empty($eqPivotIds)) continue;

                $completedCount = DB::table('tbl_progreso_retos')
                    ->whereIn('id_equipo_usuario', $eqPivotIds)
                    ->where('completado', true)
                    ->distinct('id_reto')
                    ->count('id_reto');
                
                if ($completedCount >= $retos->count()) {
                    $winnerInfo = [
                        'id' => $eq->id,
                        'nombre' => $eq->nombre_equipo,
                        'jugadores' => $eq->integrantes->pluck('nombre')->implode(', '),
                    ];
                    break;
                }
            }
        }

        // Obtener integrantes del equipo que NO han completado el reto actual
        $integrantesPendientes = [];
        if ($currentReto && !$allTeamRetosCompleted) {
            $integrantesCompletadosIds = DB::table('tbl_progreso_retos')
                ->where('id_reto', $currentReto->id)
                ->where('completado', true)
                ->whereIn('id_equipo_usuario', $teamPivotIds)
                ->pluck('id_equipo_usuario')
                ->all();

            $integrantesPendientesIds = array_diff($teamPivotIds, $integrantesCompletadosIds);

            if (!empty($integrantesPendientesIds)) {
                $integrantesPendientes = DB::table('tbl_equipo_usuarios')
                    ->join('tbl_usuarios', 'tbl_equipo_usuarios.id_usuario', '=', 'tbl_usuarios.id')
                    ->whereIn('tbl_equipo_usuarios.id', $integrantesPendientesIds)
                    ->select('tbl_usuarios.nombre', 'tbl_usuarios.foto')
                    ->get();
            }
        }

        return [
            'teamCompletedRetoIds' => $teamCompletedRetoIds,
            'currentReto' => $currentReto,
            'teamMemberCount' => $teamMemberCount,
            'integrantesCompletadosCurrent' => $integrantesCompletadosCurrent,
            'userCompletedCurrent' => $userCompletedCurrent,
            'userWaiting' => $userWaiting,
            'allTeamRetosCompleted' => $allTeamRetosCompleted,
            'integrantesPendientes' => $integrantesPendientes,
            'winner' => $winnerInfo, // info del ganador (si existe)
        ];
    }

    private function snapshotHistorial(Sala $sala): void
    {
        $totalRetos = Prueba::with('lugar')->where('id_sala', $sala->id)->orderBy('orden')->get();
        $startTime = $sala->fecha_inicio
            ? \Carbon\Carbon::parse($sala->fecha_inicio)
            : \Carbon\Carbon::parse($sala->created_at);

        $equipos = Equipo::where('numero_equipo', $sala->id)->with('integrantes')->get();

        // Determine winner team
        $winnerTeamId = null;
        foreach ($equipos as $eq) {
            $pivotIds = DB::table('tbl_equipo_usuarios')->where('id_equipo', $eq->id)->pluck('id');
            $minCompleted = null;
            foreach ($pivotIds as $pid) {
                $c = DB::table('tbl_progreso_retos')->where('id_equipo_usuario', $pid)->where('completado', true)->count();
                if ($minCompleted === null || $c < $minCompleted) $minCompleted = $c;
            }
            if ($totalRetos->count() > 0 && ($minCompleted ?? 0) >= $totalRetos->count()) {
                $winnerTeamId = $eq->id;
                break;
            }
        }

        foreach ($equipos as $eq) {
            $isWinner = $eq->id === $winnerTeamId;
            $teamPivotRows = DB::table('tbl_equipo_usuarios')->where('id_equipo', $eq->id)->get();

            foreach ($teamPivotRows as $pivot) {
                // Skip if already snapshotted (idempotent)
                $already = DB::table('tbl_historial')
                    ->where('id_sala', $sala->id)
                    ->where('id_equipo', $eq->id)
                    ->where('id_usuario', $pivot->id_usuario)
                    ->where('fecha_inicio', $sala->fecha_inicio)
                    ->exists();
                if ($already) continue;

                // Build reto snapshot for this user
                $retosSnapshot = [];
                foreach ($totalRetos as $reto) {
                    $fechaComp = DB::table('tbl_progreso_retos')
                        ->where('id_equipo_usuario', $pivot->id)
                        ->where('id_reto', $reto->id)
                        ->where('completado', true)
                        ->value('fecha_completado');
                    $retosSnapshot[] = [
                        'orden'            => $reto->orden,
                        'nombre'           => $reto->lugar?->nombre ?? ('Reto #' . $reto->orden),
                        'completado'       => (bool) $fechaComp,
                        'fecha_completado' => $fechaComp,
                    ];
                }

                // Calculate total time for this user
                $lastFecha = DB::table('tbl_progreso_retos')
                    ->where('id_equipo_usuario', $pivot->id)
                    ->where('completado', true)
                    ->max('fecha_completado');
                $totalSeconds = $lastFecha ? abs(\Carbon\Carbon::parse($lastFecha)->diffInSeconds($startTime)) : 0;

                DB::table('tbl_historial')->insert([
                    'id_sala'               => $sala->id,
                    'id_equipo'             => $eq->id,
                    'id_usuario'            => $pivot->id_usuario,
                    'resultado'             => $isWinner ? 'victoria' : 'derrota',
                    'tiempo_total_segundos' => $totalSeconds,
                    'fecha_inicio'          => $sala->fecha_inicio,
                    'fecha_fin'             => $sala->fecha_fin,
                    'retos_completados'     => json_encode($retosSnapshot),
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);
            }
        }
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
