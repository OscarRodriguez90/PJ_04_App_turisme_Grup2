<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Gimcana completada - GeoTurismo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/gimcana/final.css') }}">
</head>
<body>
    <main class="final-shell">
        <section class="final-card">
            <div class="final-icon" aria-hidden="true">
                <i class="bi bi-trophy-fill"></i>
            </div>

            <p class="eyebrow">Gimcana completada</p>
            <h1>Has acabado todos los retos</h1>
            <p class="subtitle">
                Enhorabuena, equipo <strong>{{ $equipo->nombre_equipo }}</strong>. Habéis completado
                <strong>{{ $retosCompletados }}/{{ $retosTotales }}</strong> retos en la sala
                <strong>{{ $sala->nombre }}</strong>.
            </p>

            @if(session('success'))
                <p class="flash-ok">{{ session('success') }}</p>
            @endif

            <a href="{{ route('sala.show', $sala->id) }}" class="btn-primary">
                <i class="bi bi-arrow-return-left"></i>
                Volver a sala
            </a>
        </section>
    </main>
</body>
</html>
