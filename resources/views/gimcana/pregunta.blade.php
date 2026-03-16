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
            <a href="{{ route('grupos.index') }}" class="header-back" aria-label="Volver a grupos">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1>Resolver_Prueba</h1>
            <span class="header-spacer" aria-hidden="true"></span>
        </header>

        <main class="screen-content">
            <section class="arrival-card" aria-label="Ubicacion actual">
                <div class="arrival-icon" aria-hidden="true">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <h2>¡Has llegado al punto!</h2>
                <p>La Catedral Metropolitana</p>
            </section>

            <section class="question-card" aria-label="Pregunta de la gincana">
                <article class="question-box">
                    <h3>Prueba #3</h3>
                    <p>
                        "Custodio de piedra, testigo del tiempo. Cuentan las leyendas que nunca duerme.
                        ¿Cuantos leones resguardan la puerta principal de este sagrado templo?"
                    </p>
                </article>

                <div class="answer-group">
                    <input
                        type="text"
                        placeholder="Escribe tu respuesta..."
                        aria-label="Respuesta"
                    >
                    <button type="button" class="btn-primary" disabled>
                        <i class="bi bi-check-circle"></i>
                        Validar respuesta
                    </button>
                    <a href="{{ route('gimcana.progreso') }}" class="btn-ghost">
                        <i class="bi bi-signpost-split"></i>
                        Ver progreso
                    </a>
                </div>

                <section class="group-state" aria-label="Estado del grupo">
                    <header>
                        <h4>Estado del Grupo</h4>
                        <span>3/4 en posicion</span>
                    </header>
                    <div class="avatars" aria-hidden="true">
                        <div class="avatar">A</div>
                        <div class="avatar">L</div>
                        <div class="avatar">M</div>
                        <div class="avatar avatar-off">R</div>
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
            <a href="{{ route('gimcana.pregunta') }}" class="nav-item active" aria-current="page">
                <i class="bi bi-ticket-perforated"></i>
                <span>Gimcana</span>
            </a>
            <a href="{{ route('cliente.index') }}" class="nav-item">
                <i class="bi bi-person"></i>
                <span>Perfil</span>
            </a>
        </nav>
    </div>
</body>
</html>
