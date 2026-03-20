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
                <span class="eyebrow">Guía turística</span>
                <h1>Geoturismo: Tu próxima aventura comienza aquí.</h1>
                <p>
                    Explora puntos de interés únicos, organiza tus favoritos y navega con confianza. 
                    GeoTurismo transforma tu dispositivo en el guía definitivo para descubrir cada rincón del mundo.
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
                <div class="logo-showcase">
                    <div class="logo-stack">
                        <div class="logo-wrapper">
                            <img src="{{ asset('img/admin/logo.png') }}" alt="GeoTurismo Logo" class="main-logo">
                        </div>
                        <div class="puntos-wrapper">
                            <img src="{{ asset('img/admin/puntos.jpeg') }}" alt="Puntos Interés" class="puntos-img">
                        </div>
                    </div>
                    <div class="logo-glow"></div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
