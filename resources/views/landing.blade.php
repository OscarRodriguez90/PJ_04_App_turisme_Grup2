<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeoTurismo - Descubre lugares cerca de ti</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/cliente/cliente.css') }}">
</head>
<body class="landing-body">
    <main class="landing-shell">
        <section class="landing-hero">
            <div class="landing-copy">
                <span class="eyebrow">Guía turística mobile-first</span>
                <h1>Explora lugares de interés, guarda favoritos y llega con ruta guiada.</h1>
                <p>
                    GeoTurismo te permite descubrir puntos de interés en un mapa interactivo, filtrar por categorías,
                    ver tus favoritos personales y obtener la ruta desde tu ubicación actual.
                </p>

                <div class="landing-actions">
                    <a href="{{ route('register') }}" class="btn btn-primary">Crear cuenta</a>
                    <a href="{{ route('login') }}" class="btn btn-secondary">Iniciar sesión</a>
                </div>

                <ul class="landing-features">
                    <li>Registro obligatorio para acceder a la guía.</li>
                    <li>Mapa con lugares del administrador y favoritos personales.</li>
                    <li>Filtros por favoritos, categorías y radio en metros.</li>
                    <li>Diseño pensado primero para móvil y adaptado a ordenador.</li>
                </ul>
            </div>

            <div class="landing-preview">
                <div class="phone-frame">
                    <div class="phone-screen">
                        <div class="preview-header">
                            <span>GeoTurismo</span>
                            <span>En ruta</span>
                        </div>
                        <div class="preview-map"></div>
                        <div class="preview-card">
                            <strong>Hotel Porta Fira</strong>
                            <p>Activa tu ubicación y obtén la mejor ruta hasta el lugar elegido.</p>
                            <div class="preview-tags">
                                <span>Favoritos</span>
                                <span>Hoteles</span>
                                <span>500 m</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
