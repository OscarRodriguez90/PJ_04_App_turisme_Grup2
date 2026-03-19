<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>¡Victoria! - GeoTurismo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/gimcana/mapa.css') }}">
    <link rel="stylesheet" href="{{ asset('css/gimcana/final.css') }}">
</head>
<body>
    <div class="victory-page">
        <header class="victory-header">
            <i class="bi bi-trophy-fill trophy-icon"></i>
            <h1 class="victory-title">¡ENHORABUENA!</h1>
            <p>Habéis completado la gimcana antes que nadie.</p>
        </header>

        <section class="stats-container">
            <div class="total-time-card">
                <h3>TIEMPO TOTAL</h3>
                <div class="total-time-value">{{ $stats['totalTime'] }}</div>
            </div>

            <div class="reto-list">
                @foreach($stats['retos'] as $reto)
                    <div class="reto-stat-item">
                        <div class="reto-number">{{ $reto['orden'] }}</div>
                        <div class="reto-info">
                            <h4>{{ $reto['lugar'] }}</h4>
                            <p>Finalizado a las {{ $reto['finalizado_at'] }}</p>
                        </div>
                        <div class="reto-time">+{{ $reto['duracion'] }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="victory-actions">
            <a href="{{ route('sala.index') }}" class="btn-finish">
                VOLVER AL LOBBY
            </a>
        </div>
    </div>

    <!-- Script simple para confeti -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const count = 200;
            const defaults = {
                origin: { y: 0.7 },
                colors: ['#0ea5a4', '#fbbf24', '#0ea5e9']
            };

            function fire(particleRatio, opts) {
                confetti({
                    ...defaults,
                    ...opts,
                    particleCount: Math.floor(count * particleRatio)
                });
            }

            fire(0.25, { spread: 26, startVelocity: 55 });
            fire(0.2, { spread: 60 });
            fire(0.35, { spread: 100, decay: 0.91, scalar: 0.8 });
            fire(0.1, { spread: 120, startVelocity: 25, decay: 0.92, scalar: 1.2 });
            fire(0.1, { spread: 120, startVelocity: 45 });
        });
    </script>
</body>
</html>
