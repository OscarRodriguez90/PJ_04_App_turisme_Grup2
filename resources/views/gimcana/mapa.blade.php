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
    <link rel="stylesheet" href="{{ asset('css/gimcana/mapa.css') }}">
</head>
<body>
    <div class="gimcana-app">
        <header class="screen-header">
            <a href="{{ route('sala.show', $sala->id) }}" class="header-back" aria-label="Volver a sala">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1>Siguiente punto</h1>
            <span class="header-spacer" aria-hidden="true"></span>
        </header>

        <main class="screen-content">
            @if(session('success'))
                <div class="alert-ok">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <section class="status-card">
                <p class="eyebrow">Equipo</p>
                <h2>{{ $equipo->nombre_equipo }}</h2>
                <p>Reto {{ $retoActual->orden }} de {{ $sala->pruebas()->count() }}</p>
            </section>

            <section class="hint-card">
                <p class="eyebrow">Pista del reto</p>
                <p class="hint-text">{{ $retoActual->pista }}</p>
            </section>

            <section class="map-card" aria-label="Mapa del destino">
                <div id="reto-map"></div>
            </section>

            <section class="location-card">
                <p class="eyebrow">Destino actual</p>
                <h3>{{ $retoActual->lugar->nombre ?? 'Lugar del reto' }}</h3>
                <p>{{ $retoActual->lugar->direccion_completa ?? 'Direccion no disponible' }}</p>
            </section>

            <a href="{{ route('gimcana.pregunta', ['reto' => $retoActual->id]) }}" class="btn-primary">
                <i class="bi bi-geo-alt-fill"></i>
                Ya hemos llegado
            </a>

            <a href="{{ route('gimcana.progreso') }}" class="btn-ghost">
                <i class="bi bi-list-check"></i>
                Ver progreso
            </a>
        </main>
    </div>

    <script>
        window.gimcanaMapaData = {
            lat: @json((float) ($retoActual->lugar->latitud ?? 41.3874)),
            lng: @json((float) ($retoActual->lugar->longitud ?? 2.1686)),
            nombre: @json($retoActual->lugar->nombre ?? 'Destino')
        };
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="{{ asset('js/gimcana/mapa.js') }}"></script>
</body>
</html>
