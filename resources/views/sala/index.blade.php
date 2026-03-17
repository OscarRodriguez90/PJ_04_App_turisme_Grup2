<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Salas disponibles – GeoTurismo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/sala/sala.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sala/index.css') }}">
</head>
<body class="salas-body">

    <div class="salas-container">
        <div class="salas-header">
            <h1>🗺️ GeoTurismo</h1>
            <p>Elige una gimcana para comenzar tu aventura</p>
        </div>

        @if(session('error'))
            <div class="sala-alert sala-alert--error sala-alert-centered">
                {{ session('error') }}
            </div>
        @endif

        @if($salas->count() > 0)
            <div class="salas-grid">
                @foreach($salas as $sala)
                    <div class="sala-card">
                        <div class="sala-card-header">
                            <h2 class="sala-card-title">{{ $sala->nombre }}</h2>
                            <span class="sala-card-estado sala-card-estado--{{ $sala->estado }}">
                                {{ ucfirst($sala->estado) }}
                            </span>
                        </div>

                        @if($sala->descripcion)
                            <p class="sala-card-desc">{{ $sala->descripcion }}</p>
                        @endif

                        <div class="sala-card-meta">
                            <div class="sala-card-meta-item">
                                <i class="bi bi-people"></i>
                                <span>{{ $sala->equipos_count }} equipo{{ $sala->equipos_count !== 1 ? 's' : '' }}</span>
                            </div>
                        </div>

                        <div class="sala-card-actions">
                            <a href="{{ route('sala.entrar', $sala->id) }}" class="btn-unirse">
                                <i class="bi bi-door-open"></i> Entrar
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="salas-empty">
                <i class="bi bi-inbox"></i>
                <p>No hay salas disponibles en este momento</p>
            </div>
        @endif

        <div class="salas-footer">
            <form method="POST" action="{{ route('logout') }}" class="logout-inline-form">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </div>

</body>
</html>
