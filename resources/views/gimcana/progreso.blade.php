<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Progreso Gimcana - GeoTurismo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/gimcana/progreso.css') }}">
</head>
<body>
    <div class="gimcana-app">
        <header class="screen-header">
            <a href="{{ route('gimcana.mapa') }}" class="header-back" aria-label="Volver">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1>Progreso gimcana</h1>
            <img src="{{ $avatarUrl }}" alt="Foto de perfil" class="profile-avatar">
        </header>

        <main class="screen-content">
            <h2 class="page-title">Progreso de la Gimcana</h2>

            @if(session('success'))
                <div class="alert-waiting" role="status">
                    <i class="bi bi-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <ol class="timeline" aria-label="Puntos de la gimcana">
                @foreach($retos as $reto)
                    @php
                        $completado = in_array((int) $reto->id, $completadosIds, true);
                        $actual = !$completado && $ordenActual === $reto->orden;
                        $bloqueado = !$completado && !$actual;
                        $itemClass = $completado ? 'tl-done' : ($actual ? 'tl-current' : 'tl-locked');
                        $isLast = $loop->last ? ' tl-last' : '';
                    @endphp
                    <li class="tl-item {{ $itemClass }}{{ $isLast }}">
                        <div class="tl-icon" aria-hidden="true">
                            @if($completado)
                                <i class="bi bi-check-lg"></i>
                            @elseif($actual)
                                <i class="bi bi-geo-alt-fill"></i>
                            @else
                                <i class="bi bi-lock-fill"></i>
                            @endif
                        </div>
                        @if(!$loop->last)
                            <div class="tl-connector" aria-hidden="true"></div>
                        @endif
                        <div class="tl-card {{ $completado ? 'tl-card--done' : ($actual ? 'tl-card--current' : 'tl-card--locked') }}">
                            <p class="tl-label">
                                PUNTO {{ $reto->orden }}
                                @if($completado)
                                    <span class="tl-status tl-status--done">• COMPLETADO</span>
                                @elseif($actual)
                                    <span class="tl-status tl-status--current">• DESTINO ACTUAL</span>
                                @else
                                    <span class="tl-status tl-status--locked">• BLOQUEADO</span>
                                @endif
                            </p>
                            <p class="tl-name">{{ $bloqueado ? 'Desconocido' : ($reto->lugar->nombre ?? 'Lugar del reto') }}</p>

                            @if($actual)
                                <a href="{{ route('gimcana.mapa') }}" class="btn-location">
                                    <i class="bi bi-send"></i>
                                    Ir al punto
                                </a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
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
    <script src="{{ asset('js/gimcana/progreso.js') }}"></script>
</body>
</html>
