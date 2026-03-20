<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mi Historial – GeoTurismo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/sala/historial.css') }}">
</head>
<body>
    <div class="historial-container">
        <div class="historial-header">
            <a href="{{ route('sala.index') }}" aria-label="Volver">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1>Mi Historial</h1>
        </div>

        @if(count($historial) === 0)
            <div class="historial-empty">
                <i class="bi bi-calendar-x"></i>
                <p>Aún no has participado en ninguna gimcana.</p>
            </div>
        @else
            @foreach($historial as $entrada)
                @php
                    $resultado = $entrada['resultado'];
                    $sala = $entrada['sala'];
                    $equipo = $entrada['equipo'];
                    $totalSeconds = $entrada['totalSeconds'];
                    $mins = intdiv($totalSeconds, 60);
                    $secs = $totalSeconds % 60;
                    $timeStr = $totalSeconds > 0 ? ($mins > 0 ? "{$mins}m {$secs}s" : "{$secs}s") : '—';

                    $badgeClass = $resultado === 'victoria' ? 'badge-victoria' : ($resultado === 'derrota' ? 'badge-derrota' : 'badge-en_curso');
                    $badgeIcon = $resultado === 'victoria' ? 'bi-trophy-fill' : ($resultado === 'derrota' ? 'bi-flag-fill' : 'bi-hourglass-split');
                    $badgeText = $resultado === 'victoria' ? 'Victoria' : ($resultado === 'derrota' ? 'Derrota' : 'En curso');
                @endphp
                <div class="historial-item {{ $resultado }}" onclick="this.classList.toggle('expanded')">
                    <div class="historial-item-header">
                        <div>
                            <p class="historial-sala-name">{{ $sala->nombre }}</p>
                            <p class="historial-equipo-name">Equipo: {{ $equipo->nombre_equipo }}</p>
                        </div>
                        <span class="historial-badge {{ $badgeClass }}">
                            <i class="bi {{ $badgeIcon }}"></i> {{ $badgeText }}
                        </span>
                    </div>

                    <div class="historial-meta">
                        <span><i class="bi bi-clock"></i> Tiempo: {{ $timeStr }}</span>
                        <span><i class="bi bi-people"></i> {{ $equipo->integrantes->count() }} integrante{{ $equipo->integrantes->count() !== 1 ? 's' : '' }}</span>
                        @if($entrada['fecha'])
                            <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($entrada['fecha'])->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>

                    <button class="expand-toggle">
                        <i class="bi bi-chevron-down"></i> Ver retos
                    </button>

                    <div class="reto-list">
                        @foreach($entrada['retos'] as $reto)
                            <div class="reto-mini-item {{ $reto['completado'] ? 'done' : 'not-done' }}">
                                <span class="reto-mini-num">{{ $reto['orden'] }}</span>
                                <span class="reto-mini-name">{{ $reto['nombre'] }}</span>
                                <span class="reto-mini-time">
                                    @if($reto['completado'])
                                        {{ \Carbon\Carbon::parse($reto['fecha_completado'])->format('H:i:s') }}
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</body>
</html>
