<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Resolver Prueba - GeoTurismo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/gimcana/pregunta.css') }}">
</head>
<body>
    <div class="gimcana-app">
        <header class="screen-header">
            <a href="{{ route('gimcana.mapa') }}" class="header-back" aria-label="Volver al mapa">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1>Resolver reto</h1>
            <img src="{{ $avatarUrl }}" alt="Foto de perfil" class="profile-avatar">
        </header>

        <main class="screen-content">
            @if(session('success'))
                <section class="group-state" aria-label="Mensaje">
                    <header>
                        <h4>{{ session('success') }}</h4>
                    </header>
                </section>
            @endif

            <section class="arrival-card" aria-label="Ubicacion actual">
                <div class="arrival-icon" aria-hidden="true">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <h2>¡Has llegado al punto!</h2>
                <p>{{ $retoActual->lugar->nombre ?? 'Lugar del reto' }}</p>

                <button
                    type="button"
                    id="locateBtn"
                    class="btn-locate"
                    aria-label="Obtener mi ubicación"
                    data-lat="{{ (float) ($retoActual->lugar->latitud ?? 41.3874) }}"
                    data-lng="{{ (float) ($retoActual->lugar->longitud ?? 2.1686) }}"
                    data-nombre="{{ $retoActual->lugar->nombre ?? 'Destino' }}"
                >
                    <i class="bi bi-crosshair2"></i>
                    Obtener mi ubicación
                </button>

                <div id="locationMessage" class="location-message"></div>

                <div class="location-section">
                    <div class="location-box">
                        <span class="location-label">Tu ubicación</span>
                        <span class="location-value" id="userLocDisplay">—</span>
                    </div>
                    <div class="location-box">
                        <span class="location-label">Distancia</span>
                        <span class="location-value" id="distanceDisplay">—</span>
                    </div>
                </div>
            </section>

            <section class="question-card" aria-label="Pregunta de la gincana">
                <article class="question-box">
                    <h3>Reto #{{ $retoActual->orden }}</h3>
                    <p>{{ $retoActual->pregunta }}</p>
                </article>

                <form class="answer-group" method="POST" action="{{ route('gimcana.pregunta.resolver', ['reto' => $retoActual->id]) }}">
                    @csrf
                    <input
                        type="text"
                        name="respuesta"
                        value="{{ old('respuesta') }}"
                        placeholder="Escribe tu respuesta..."
                        aria-label="Respuesta"
                    >
                    @error('respuesta')
                        <span class="answer-error">{{ $message }}</span>
                    @enderror
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-check-circle"></i>
                        Validar respuesta
                    </button>
                    <a href="{{ route('gimcana.progreso') }}" class="btn-ghost">
                        <i class="bi bi-signpost-split"></i>
                        Ver progreso
                    </a>
                </form>

                <section class="group-state" aria-label="Estado del grupo">
                    <header>
                        <h4>Estado del Grupo</h4>
                        <span>{{ $integrantesEnReto }}/{{ $equipo->integrantes->count() }} completado</span>
                    </header>
                    <div class="avatars" aria-hidden="true">
                        @foreach($equipo->integrantes as $integrante)
                            <div class="avatar">{{ strtoupper(substr($integrante->nombre, 0, 1)) }}</div>
                        @endforeach
                    </div>
                </section>
            </section>
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
            <a href="{{ route('gimcana.mapa') }}" class="nav-item active" aria-current="page">
                <i class="bi bi-ticket-perforated"></i>
                <span>Gimcana</span>
            </a>
            <a href="{{ route('cliente.index') }}" class="nav-item">
                <i class="bi bi-person"></i>
                <span>Perfil</span>
            </a>
        </nav>
    </div>

    <script src="{{ asset('js/gimcana/pregunta.js') }}"></script>
</body>
</html>
