<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Progreso Gimcana - GeoTurismo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/gimcana/progreso.css') }}">
</head>
<body>
    <div class="gimcana-app">
        <header class="screen-header">
            <a href="{{ route('gimcana.pregunta') }}" class="header-back" aria-label="Volver">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1>Progreso_gimcana</h1>
            <span class="header-spacer" aria-hidden="true"></span>
        </header>

        <main class="screen-content">
            <h2 class="page-title">Progreso de la Gimcana</h2>

            <div class="alert-waiting" role="status">
                <i class="bi bi-clock"></i>
                <span>Esperando a que todo tu equipo llegue al punto 2</span>
            </div>

            <ol class="timeline" aria-label="Puntos de la gimcana">
                <li class="tl-item tl-done">
                    <div class="tl-icon" aria-hidden="true">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div class="tl-connector" aria-hidden="true"></div>
                    <div class="tl-card tl-card--done">
                        <p class="tl-label">PUNTO 1 <span class="tl-status tl-status--done">• COMPLETADO</span></p>
                        <p class="tl-name">La Catedral Metropolitana</p>
                    </div>
                </li>

                <li class="tl-item tl-current">
                    <div class="tl-icon" aria-hidden="true">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div class="tl-connector" aria-hidden="true"></div>
                    <div class="tl-card tl-card--current">
                        <p class="tl-label">PUNTO 2 <span class="tl-status tl-status--current">• DESTINO ACTUAL</span></p>
                        <p class="tl-name">
                            <span class="tl-riddle-badge">
                                <i class="bi bi-question-lg" aria-hidden="true"></i>
                                El custodio de piedra
                            </span>
                        </p>
                        <a href="{{ route('gimcana.pregunta') }}" class="btn-location">
                            <i class="bi bi-send"></i>
                            Ir a la pregunta
                        </a>
                    </div>
                </li>

                <li class="tl-item tl-locked">
                    <div class="tl-icon" aria-hidden="true">
                        <i class="bi bi-lock-fill"></i>
                    </div>
                    <div class="tl-connector" aria-hidden="true"></div>
                    <div class="tl-card tl-card--locked">
                        <p class="tl-label">PUNTO 3 <span class="tl-status tl-status--locked">• BLOQUEADO</span></p>
                        <p class="tl-name">Desconocido</p>
                    </div>
                </li>

                <li class="tl-item tl-locked tl-last">
                    <div class="tl-icon" aria-hidden="true">
                        <i class="bi bi-lock-fill"></i>
                    </div>
                    <div class="tl-card tl-card--locked">
                        <p class="tl-label">PUNTO 4 <span class="tl-status tl-status--locked">• BLOQUEADO</span></p>
                        <p class="tl-name">Desconocido</p>
                    </div>
                </li>
            </ol>
        </main>

        <nav class="bottom-nav" aria-label="Navegacion principal">
            <a href="{{ route('cliente.index') }}" class="nav-item">
                <i class="bi bi-map"></i>
                <span>Mapa</span>
            </a>
            <a href="{{ route('cliente.index') }}" class="nav-item">
                <i class="bi bi-heart"></i>
                <span>Favoritos</span>
            </a>
            <a href="{{ route('gimcana.progreso') }}" class="nav-item active" aria-current="page">
                <i class="bi bi-ticket-perforated"></i>
                <span>Gimcana</span>
            </a>
            <a href="{{ route('cliente.index') }}" class="nav-item">
                <i class="bi bi-person"></i>
                <span>Perfil</span>
            </a>
        </nav>
    </div>
    <script src="{{ asset('js/gimcana/progreso.js') }}"></script>
</body>
</html>
