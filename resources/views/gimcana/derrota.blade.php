<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Gimcana Finalizada - GeoTurismo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/gimcana/mapa.css') }}">
    <link rel="stylesheet" href="{{ asset('css/gimcana/derrota.css') }}">
    <link rel="stylesheet" href="{{ asset('css/gimcana/final.css') }}">
</head>
<body>
    <div class="defeat-page">
        <header class="defeat-header">
            <i class="bi bi-flag-fill broken-heart-icon"></i>
            <h1 class="defeat-title">GIMICANA FINALIZADA</h1>
            <p>Otro equipo ha cruzado la meta primero.</p>
        </header>

        <section class="winner-announce-card">
            <h3>EQUIPO GANADOR</h3>
            <span class="winner-team-name">{{ $winner['nombre'] }}</span>
            <p class="winner-players">Integrantes: {{ $winner['jugadores'] }}</p>
        </section>

        <h3 class="personal-stats-title">Vuestras Estadísticas</h3>
        
        <section class="stats-container" style="background: rgba(255,255,255,0.6); box-shadow: none; border: 1px solid #e2e8f0;">
            <div class="total-time-card" style="background: linear-gradient(135deg, #64748b, #94a3b8);">
                <h3>TIEMPO TRANSCURRIDO</h3>
                <div class="total-time-value">{{ $stats['totalTime'] }}</div>
            </div>

            <div class="reto-list">
                @foreach($stats['retos'] as $reto)
                    <div class="reto-stat-item">
                        <div class="reto-number" style="background: #94a3b8;">{{ $reto['orden'] }}</div>
                        <div class="reto-info">
                            <h4>{{ $reto['lugar'] }}</h4>
                            @if($reto['finalizado_at'] !== '-')
                                <p>Finalizado a las {{ $reto['finalizado_at'] }}</p>
                            @else
                                <p>No completado</p>
                            @endif
                        </div>
                        <div class="reto-time" style="color: #64748b;">
                            {{ $reto['finalizado_at'] !== '-' ? '+' . $reto['duracion'] : '-' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <a href="{{ route('sala.index') }}" class="btn-lobby">
            VOLVER AL LOBBY
        </a>
    </div>
</body>
</html>
