<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GeoTurismo - Cliente</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css">
    <link rel="stylesheet" href="{{ asset('css/cliente/cliente.css') }}">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand-block">
                <div class="brand-row">
                    <img src="{{ asset('img/admin/logo.png') }}" alt="GeoTurismo" class="brand-logo">
                    <div>
                    <p class="eyebrow">Área cliente</p>
                    <h1>GeoTurismo</h1>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost">Salir</button>
                </form>
            </div>

            <div class="user-card">
                <div class="avatar">{{ strtoupper(substr($usuario->nombre, 0, 1)) }}</div>
                <div>
                    <strong>{{ $usuario->nombre }} {{ $usuario->apellido1 }}</strong>
                    <p>{{ '@' . $usuario->username }}</p>
                </div>
            </div>

            <section class="panel-section">
                <div class="section-title-row">
                    <h2>Filtros del mapa</h2>
                    <button type="button" class="btn-link" id="locateMeButton">Mi ubicación</button>
                </div>

                <label class="filter-label" for="searchInput">Buscar lugar</label>
                <input id="searchInput" class="input" type="search" placeholder="Nombre o dirección">

                <div class="switch-row">
                    <label class="switch-card">
                        <input type="checkbox" id="favoritesOnly">
                        <span>Solo favoritos</span>
                    </label>
                    <label class="switch-card">
                        <input type="checkbox" id="nearbyOnly">
                        <span>Solo cerca de mí</span>
                    </label>
                </div>

                <label class="filter-label" for="radiusRange">Distancia máxima: <span id="radiusValue">2000</span> m</label>
                <input id="radiusRange" type="range" min="100" max="5000" step="100" value="2000">

                <h3 class="subheading">Categorías</h3>
                <div class="chips-grid">
                    @foreach ($categorias as $categoria)
                        <label class="chip-option">
                            <input type="checkbox" class="category-filter" value="{{ $categoria->id }}" checked>
                            <span>{{ $categoria->nombre }}</span>
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="panel-section stats-section">
                <div class="mini-stat">
                    <span>Lugares visibles</span>
                    <strong id="visibleCount">0</strong>
                </div>
                <div class="mini-stat">
                    <span>Tus favoritos</span>
                    <strong id="favoritesCount">{{ count($favoritosIds) }}</strong>
                </div>
            </section>

            <section class="panel-section list-section">
                <div class="section-title-row">
                    <h2>Lugares</h2>
                    <span id="resultsLabel">0 resultados</span>
                </div>
                <div id="placesList" class="places-list"></div>
            </section>
        </aside>

        <main class="content-area">
            <header class="mobile-topbar">
                <div>
                    <p class="eyebrow">Explorar</p>
                    <h2>Mapa interactivo</h2>
                </div>
                <button type="button" class="btn btn-secondary" id="toggleSidebar">Filtros</button>
            </header>

            <section class="map-wrapper">
                <div id="map"></div>
                <div class="floating-message" id="mapMessage">
                    Toca un marcador para abrir la ficha del lugar y calcular la ruta.
                </div>
            </section>

            <aside class="detail-panel" id="detailPanel">
                <div class="detail-panel__empty" id="emptyState">
                    <h3>Selecciona un lugar</h3>
                    <p>Verás aquí el resumen, la dirección, la categoría y el acceso a la ruta desde tu posición.</p>
                </div>

                <article class="detail-card hidden" id="placeDetail">
                    <div class="detail-header">
                        <div>
                            <p class="eyebrow" id="detailCategory">Categoría</p>
                            <h3 id="detailName">Lugar</h3>
                        </div>
                        <button type="button" class="favorite-button" id="detailFavoriteButton">♡</button>
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
                    </dl>

                    <div class="detail-actions">
                        <button type="button" class="btn btn-primary" id="routeButton">Mostrar ruta</button>
                        <button type="button" class="btn btn-secondary" id="centerButton">Centrar mapa</button>
                    </div>

                    <p class="route-note" id="routeNote">Necesitaremos tu ubicación actual para calcular la ruta.</p>
                </article>
            </aside>
        </main>
    </div>

    <script>
        window.geoTurismoData = {
            lugares: @json($lugares),
            favoritosIds: @json($favoritosIds),
            toggleFavoritoUrl: "{{ url('/cliente/favoritos') }}",
            usuario: {
                nombre: @json($usuario->nombre),
                username: @json($usuario->username)
            }
        };
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <script src="{{ asset('js/cliente/cliente.js') }}"></script>
</body>
</html>
