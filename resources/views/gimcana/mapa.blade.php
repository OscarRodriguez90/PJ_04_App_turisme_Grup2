<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Gimcana - GeoTurismo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css">
    <link rel="stylesheet" href="{{ asset('css/gimcana/mapa.css') }}">
</head>
<body>
    <div id="map"></div>

    <header class="ui-header">
        <div class="logo-box">
            <img src="{{ asset('img/admin/logo.png') }}" alt="GeoTurismo" class="brand-logo">
            <h1>GeoTurismo</h1>
        </div>
        <div class="team-pill">
            <span class="team-label">EQUIPO</span>
            <span id="team-name">{{ $equipo->nombre_equipo }} @if(isset($usuario)) ({{ $usuario->nombre }}) @endif</span>
        </div>
    </header>

    <!-- Pista del reto -->
    <section class="hint-card">
        <p class="eyebrow">Reto {{ $retoActual->orden }} de {{ $sala->pruebas()->count() }}</p>
        <p class="hint-text">{{ $retoActual->pista }}</p>
    </section>

    <!-- D-Pad Simulación (Copiado de DescubreMap) -->
    <div class="dev-dpad-wrapper" id="dev-dpad-wrapper">
        <button id="btn-toggle-dpad" class="dpad-toggle-btn" title="Joystick de simulación">🎮</button>

        @if(session('success'))
            <div class="alert-ok">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="dev-dpad hidden" id="dev-dpad">
            <div class="dpad-row">
                <button id="btn-up-left" class="dpad-btn">↖</button>
                <button id="btn-up" class="dpad-btn">▲</button>
                <button id="btn-up-right" class="dpad-btn">↗</button>
            </div>
            <div class="dpad-row">
                <button id="btn-left" class="dpad-btn">◀</button>
                <div class="dpad-btn dpad-center"></div>
                <button id="btn-right" class="dpad-btn">▶</button>
            </div>
            <div class="dpad-row">
                <button id="btn-down-left" class="dpad-btn">↙</button>
                <button id="btn-down" class="dpad-btn">▼</button>
                <button id="btn-down-right" class="dpad-btn">↘</button>
            </div>
        </div>
    </div>

    <!-- Controles flotantes -->
    <div class="side-controls">
        <a href="{{ route('gimcana.progreso', $sala->id) }}" class="btn-side" title="Ver progreso">
            <i class="bi bi-list-check"></i>
        </a>
        <button id="btn-locate" class="btn-side" title="Centrar en mi ubicación">
            <i class="bi bi-crosshair2"></i>
        </button>
        <button id="btn-permissions" class="btn-side" title="Permisos de ubicación">
            <i class="bi bi-geo-alt"></i>
        </button>
    </div>

    <!-- Panel de detalle (Copiado de cliente.blade.php) -->
    <aside class="detail-panel" id="detailPanel">
        <div class="detail-panel__empty" id="emptyState">
            <h3>Selecciona un lugar</h3>
            <p>Verás aquí el resumen, la dirección, la categoría y el acceso a la ruta desde tu posición.</p>
        </div>

        <article class="detail-card hidden" id="placeDetail" style="position: relative;">
            <button type="button" id="closeDetailButton" style="position: absolute; top: 1.5rem; right: 1.5rem; background: rgba(0,0,0,0.5); color: #fff; border: 0; width: 32px; height: 32px; border-radius: 50%; display: grid; place-items: center; cursor: pointer; transition: .2s; z-index: 10;" onmouseover="this.style.background='rgba(0,0,0,0.7)'" onmouseout="this.style.background='rgba(0,0,0,0.5)'">
                <i class="bi bi-x-lg"></i>
            </button>
            <img id="detailImage" src="" alt="Imagen del lugar" style="width: 100%; height: 180px; object-fit: cover; border-radius: .5rem; margin-bottom: 1rem; border: 1px solid var(--border);">
            <div class="detail-header">
                <div>
                    <p class="eyebrow" id="detailCategory">Categoría</p>
                    <h3 id="detailName">Lugar</h3>
                </div>
            </div>

            <p id="detailDescription" class="detail-description"></p>
            <dl class="detail-meta">
                <div>
                    <dt>Dirección</dt>
                    <dd id="detailAddress"></dd>
                </div>
                <div>
                    <dt>Coordenadas</dt>
                    <dd id="detailCoordinates"></dd>
                </div>
                <div id="detailDistanceRow" class="hidden">
                    <dt>Distancia (GPS)</dt>
                    <dd id="detailDistance">Calculando...</dd>
                </div>
            </dl>

            <div class="detail-actions">
                <button type="button" class="btn btn-primary" id="routeButton">Mostrar ruta</button>
                <button type="button" class="btn btn-secondary" id="centerButton">Centrar mapa</button>
            </div>

            <p class="route-note" id="routeNote">Necesitaremos tu ubicación actual para calcular la ruta.</p>
        </article>
    </aside>


    <!-- Bottom Sheet para la Pregunta (Trigger automático a 150m) -->
    <div class="bottom-sheet-overlay" id="question-overlay"></div>
    <div class="bottom-sheet" id="question-sheet">
        <div class="bs-drag-handle"><span></span></div>
        <div class="bs-content">
            <p class="eyebrow">¡Has llegado al punto!</p>
            <h2>Reto #{{ $retoActual->orden }}</h2>
            <p class="question-text">{{ $retoActual->pregunta }}</p>

            <form id="answer-form" class="answer-group">
                @csrf
                <input type="text" id="answer-input" placeholder="Escribe tu respuesta..." required autofocus>
                <div id="answer-error" class="answer-error hidden"></div>
                <button type="submit" class="btn-primary-action">
                    Validar respuesta
                </button>
            </form>
        </div>
    </div>

    <!-- Overlay de GPS (Paso inicial) -->
    <div id="gps-overlay" class="gps-overlay">
        <div class="gps-modal">
            <div class="gps-icon-wrapper">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <h2>Activa tu Radar</h2>
            <p>GeoTurismo necesita acceder a tu ubicación GPS en tiempo real para verificar si has encontrado los lugares del reto.</p>
            <button id="btn-start-gps" class="main-action full-width">
                Comenzar Aventura
            </button>
        </div>
    </div>

    <!-- Toast de notificaciones -->
    <div id="toast" class="toast hidden">
        <span id="toast-message"></span>
    </div>

    <script>
        window.gimcanaData = {
            retoActual: {
                id: {{ $retoActual->id }},
                orden: {{ $retoActual->orden }},
                lat: {{ (float) $retoActual->lugar->latitud }},
                lng: {{ (float) $retoActual->lugar->longitud }},
                nombre: "{{ $retoActual->lugar->nombre }}",
                pista: "{{ $retoActual->pista }}"
            },
            lugares: @json($lugares),
            resolverUrl: "{{ route('gimcana.pregunta.resolver', ['reto' => $retoActual->id]) }}",
            ubicacionUrl: "{{ route('gimcana.ubicacion.actualizar') }}",
            csrfToken: "{{ csrf_token() }}"
        };
    </script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <!-- Usamos SweetAlert2 para mensajes de éxito/error finales si es necesario -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/gimcana/mapa.js') }}"></script>
</body>
</html>
