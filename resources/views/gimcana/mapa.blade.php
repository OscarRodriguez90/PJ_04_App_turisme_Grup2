<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Siguiente Punto - GeoTurismo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css">
    <link rel="stylesheet" href="{{ asset('css/gimcana/mapa.css') }}">
</head>
<body>
    <div class="mapa-screen" aria-label="Mapa del destino">
        <div
            id="reto-map"
            data-lat="{{ (float) ($retoActual->lugar->latitud ?? 41.3874) }}"
            data-lng="{{ (float) ($retoActual->lugar->longitud ?? 2.1686) }}"
            data-nombre="{{ $retoActual->lugar->nombre ?? 'Destino' }}"
        ></div>

        <header class="mapa-topbar">
            <a href="{{ route('sala.show', $sala->id) }}" class="header-back" aria-label="Volver a sala">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="topbar-title">
                <p class="eyebrow">Equipo {{ $equipo->nombre_equipo }}</p>
                <h1>Reto {{ $retoActual->orden }} de {{ $sala->pruebas()->count() }}</h1>
            </div>
            <img src="{{ $avatarUrl }}" alt="Foto de perfil" class="profile-avatar">
        </header>

        @if(session('success'))
            <div class="alert-ok">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <section class="hint-card">
            <p class="eyebrow">Pista del reto</p>
            <p class="hint-text">{{ $retoActual->pista }}</p>
        </section>

        <button type="button" id="locateMeButton" class="btn-locate-me" aria-label="Mi ubicación">
            <i class="bi bi-crosshair2"></i>
            Mi ubicación
        </button>

        <div id="mapMessage" class="map-message"></div>

        <section class="mapa-bottomsheet">
            <div class="location-head">
                <div>
                    <p class="eyebrow">Destino actual</p>
                    <h3>{{ $retoActual->lugar->nombre ?? 'Lugar del reto' }}</h3>
                    <p>{{ $retoActual->lugar->direccion_completa ?? 'Dirección no disponible' }}</p>
                </div>
                <div class="distance-info">
                    <p class="eyebrow">Distancia</p>
                    <p id="distanceDisplay" class="distance-text">Obtén tu ubicación</p>
                </div>
            </div>

            <div class="mapa-actions">
                <a href="{{ route('gimcana.pregunta', ['reto' => $retoActual->id]) }}" class="btn-primary">
                    <i class="bi bi-geo-alt-fill"></i>
                    Ya hemos llegado
                </a>

                <a href="{{ route('gimcana.progreso') }}" class="btn-ghost">
                    <i class="bi bi-list-check"></i>
                    Ver progreso
                </a>
            </div>
        </section>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <script src="{{ asset('js/gimcana/mapa.js') }}"></script>
</body>
</html>
