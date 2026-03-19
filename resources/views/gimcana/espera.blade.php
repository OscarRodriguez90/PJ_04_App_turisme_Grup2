<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Esperando al equipo - GeoTurismo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/gimcana/espera.css') }}">
</head>
<body>
    <div class="gimcana-app">
        <header class="screen-header">
            <a href="{{ route('gimcana.progreso') }}" class="header-back" aria-label="Volver">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1>Esperando al equipo</h1>
            <img src="{{ $avatarUrl }}" alt="Foto de perfil" class="profile-avatar">
        </header>

        <main class="screen-content">
            @if(session('success'))
                <div class="alert-ok">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <section class="wait-card">
                <div class="wait-icon" aria-hidden="true">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <h2>Reto {{ $retoActual->orden }}</h2>
                <p>Ya has completado este reto. Esperando a que tu grupo lo resuelva.</p>
            </section>

            <section class="status-card">
                <p class="eyebrow">Ubicacion actual</p>
                <h3>{{ $retoActual->lugar->nombre ?? 'Lugar del reto' }}</h3>
                <p>{{ $integrantesCompletados }}/{{ $totalIntegrantes }} integrantes completados</p>
                <p class="pending">Pendientes: {{ $miembrosPendientes }}</p>
            </section>

            <a href="{{ route('gimcana.espera') }}" class="btn-primary">
                <i class="bi bi-arrow-clockwise"></i>
                Recargar estado
            </a>

            <a href="{{ route('gimcana.progreso') }}" class="btn-ghost">
                <i class="bi bi-list-check"></i>
                Ver progreso
            </a>
        </main>
    </div>
</body>
</html>
