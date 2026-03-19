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
    <style>
        .pending-members {
            margin-top: 25px;
            text-align: left;
            background: rgba(255, 255, 255, 0.7);
            padding: 20px;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
        }
        .pending-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .member-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .member-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .member-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
            background: #f1f5f9;
        }
        .member-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: #1e293b;
        }
        .member-badge {
            margin-left: auto;
            font-size: 0.7rem;
            background: #fee2e2;
            color: #ef4444;
            padding: 2px 8px;
            border-radius: 99px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="gimcana-app">
        <header class="screen-header">
            <span class="header-spacer" aria-hidden="true"></span>
            <h1>Gimcana en curso</h1>
            <span class="header-spacer" aria-hidden="true"></span>
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
                <p class="eyebrow">EQUIPO {{ strtoupper($equipo->nombre_equipo) }}</p>
                <h2>Reto {{ $retoActual->orden }} completado</h2>
                <p>¡Buen trabajo! Ahora toca esperar a que el resto de tu equipo termine este reto para pasar al siguiente.</p>
            </section>

            @if(count($integrantesPendientes) > 0)
                <section class="pending-members">
                    <h4 class="pending-title">
                        <i class="bi bi-people-fill"></i>
                        Compañeros pendientes ({{ count($integrantesPendientes) }})
                    </h4>
                    <div class="member-list">
                        @foreach($integrantesPendientes as $integrante)
                            <div class="member-item">
                                @if($integrante->foto)
                                    <img src="{{ asset('storage/' . $integrante->foto) }}" alt="{{ $integrante->nombre }}" class="member-photo">
                                @else
                                    <img src="{{ asset('img/usuarios/default_user.png') }}" alt="{{ $integrante->nombre }}" class="member-photo">
                                @endif
                                <span class="member-name">{{ $integrante->nombre }}</span>
                                <span class="member-badge">EN CAMINO</span>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <div class="wait-actions" style="margin-top: 30px; display: flex; flex-direction: column; gap: 10px;">
                <a href="{{ route('gimcana.espera') }}" class="btn-primary">
                    <i class="bi bi-arrow-clockwise"></i>
                    Actualizar estado
                </a>
                <a href="{{ route('gimcana.progreso') }}" class="btn-ghost">
                    <i class="bi bi-list-check"></i>
                    Ver progreso general
                </a>
            </div>
        </main>
    </div>
</body>
</html>
