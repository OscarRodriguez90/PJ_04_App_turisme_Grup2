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
                            <p class="tl-name">{{ $completado ? ($reto->lugar->nombre ?? 'Lugar del reto') : 'Lugar por descubrir' }}</p>

                            @if($actual)
                                @if(count($integrantesPendientes) > 0)
                                    <div class="tl-pending-box">
                                        <p class="tl-pending-title">
                                            <i class="bi bi-people-fill"></i>
                                            Equipo pendiente ({{ count($integrantesPendientes) }})
                                        </p>
                                        <div class="tl-pending-list">
                                            @foreach($integrantesPendientes as $integrante)
                                                <div class="tl-pending-user">
                                                    <img src="{{ $integrante->foto ? asset('storage/' . $integrante->foto) : asset('img/usuarios/default_user.png') }}" 
                                                         alt="{{ $integrante->nombre }}" 
                                                         class="tl-pending-photo">
                                                    <span>{{ $integrante->nombre }}</span>
                                                    <span class="tl-pending-status">PENDIENTE</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($userWaiting)
                                    <a href="{{ route('gimcana.espera') }}" class="btn-location" style="width: 100%; background: var(--warning); color: #fff; border: none;">
                                        <i class="bi bi-hourglass-split"></i>
                                        Esperar equipo
                                    </a>
                                @else
                                    <a href="{{ route('gimcana.mapa', ['locate' => 'user']) }}" class="btn-location" style="width: 100%;">
                                        <i class="bi bi-geo-alt"></i>
                                        Ver tu ubicación
                                    </a>
                                @endif
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </main>

    </div>
    <script src="{{ asset('js/gimcana/progreso.js') }}"></script>
    <script>
        setInterval(async () => {
            try {
                const resp = await fetch("{{ route('gimcana.ubicacion.actualizar') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' },
                    body: JSON.stringify({ lat: 0, lng: 0 })
                });
                const result = await resp.json();
                if (result.gameOver && result.redirectUrl) window.location.href = result.redirectUrl;
            } catch (e) {}
        }, 1000);
    </script>
</body>
</html>
