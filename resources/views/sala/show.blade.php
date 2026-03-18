<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sala {{ $sala->nombre }} – GeoTurismo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/sala/sala.css') }}">
</head>
<body class="sala-room-body">

    {{-- ── Header ── --}}
    <header class="sala-header">
        <div class="sala-header-info">
            <span class="sala-code-badge">{{ $sala->nombre }}</span>
            <span class="sala-estado sala-estado--{{ $sala->estado }}">{{ ucfirst($sala->estado) }}</span>
        </div>
        <a href="{{ route('sala.index') }}" class="btn-ghost-small">
            <i class="bi bi-box-arrow-left"></i> Salir
        </a>
    </header>

    {{-- ── Main ── --}}
    <main class="sala-main">

        @if($miEquipo)
        {{-- User already has a group --}}
        <section class="sala-section">
            <h2 class="section-heading">
                <i class="bi bi-people-fill"></i> Mi equipo
            </h2>
            <div class="equipo-card equipo-card--mine">
                <div class="equipo-card-header">
                    <span class="equipo-nombre">{{ $miEquipo->nombre_equipo }}</span>
                    <span class="equipo-count">
                        <i class="bi bi-person"></i>
                        {{ $miEquipo->integrantes->count() }}
                    </span>
                </div>
                <div class="equipo-miembros">
                    @foreach($miEquipo->integrantes as $integrante)
                        <div class="miembro-chip">
                            <div class="miembro-avatar">{{ strtoupper(substr($integrante->nombre, 0, 1)) }}</div>
                            <span>{{ $integrante->nombre }}</span>
                            @if($integrante->id === $miEquipo->id_lider)
                                <i class="bi bi-star-fill lider-icon" title="Líder"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button class="btn-danger-small" onclick="salirEquipo()">
                    <i class="bi bi-door-open"></i> Salir del equipo
                </button>
                @if($retosCompletados)
                    <form method="POST" action="{{ route('gimcana.reiniciar') }}" class="restart-retos-form">
                        @csrf
                        <button type="submit" class="btn-outline-small btn-start-retos">
                            <i class="bi bi-arrow-counterclockwise"></i> Volver a empezar retos
                        </button>
                    </form>
                @endif
            </div>
        </section>
        @else
        {{-- No group yet --}}
        <section class="sala-section">
            <h2 class="section-heading">
                <i class="bi bi-plus-circle"></i> ¿Listo para jugar?
            </h2>
            <p class="section-help">Crea tu propio equipo o únete a uno ya creado.</p>
            <button class="btn-primary btn-crear-equipo" onclick="abrirModalCrear()">
                <i class="bi bi-shield-plus"></i> Crear equipo
            </button>
        </section>
        @endif

        {{-- ── All groups in this sala ── --}}
        <section class="sala-section">
            <h2 class="section-heading">
                <i class="bi bi-grid"></i> Equipos en esta sala
                <span class="sala-count-badge">{{ $equipos->count() }}</span>
            </h2>

            @forelse($equipos as $equipo)
                @php
                    $esMio = $miEquipo && $miEquipo->id === $equipo->id;
                    $equipoLleno = (int) $equipo->integrantes_count >= 8;
                @endphp
                <div class="equipo-card {{ $esMio ? 'equipo-card--mine' : '' }}">
                    <div class="equipo-card-header">
                        <span class="equipo-nombre">{{ $equipo->nombre_equipo }}</span>
                        <span class="equipo-count">
                            <i class="bi bi-person"></i> {{ $equipo->integrantes_count }}/8
                        </span>
                    </div>
                    <p class="equipo-lider">
                        <i class="bi bi-star"></i>
                        Líder: {{ $equipo->lider->nombre ?? '–' }}
                    </p>
                    @if(!$miEquipo)
                        <button class="btn-outline-small" onclick="unirseEquipo({{ $equipo->id }})" {{ $equipoLleno ? 'disabled' : '' }}>
                            <i class="bi {{ $equipoLleno ? 'bi-lock-fill' : 'bi-person-plus' }}"></i>
                            {{ $equipoLleno ? 'Grupo lleno' : 'Unirse' }}
                        </button>
                    @elseif($esMio)
                        <span class="badge-mine"><i class="bi bi-check-circle-fill"></i> Tu equipo</span>
                    @endif
                </div>
            @empty
                <div class="equipos-empty">
                    <i class="bi bi-people"></i>
                    <p>Aún no hay equipos. ¡Sé el primero en crear uno!</p>
                </div>
            @endforelse
        </section>

    </main>

    {{-- ── Modal: crear equipo ── --}}
    <div class="sala-modal-overlay" id="modalCrear" hidden>
        <div class="sala-modal">
            <div class="sala-modal-header">
                <h3>Crear equipo</h3>
                <button class="modal-close" onclick="cerrarModal('modalCrear')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="sala-modal-body">
                <div class="form-group">
                    <label for="input-nombre-equipo">Nombre del equipo</label>
                    <input
                        id="input-nombre-equipo"
                        type="text"
                        maxlength="50"
                        placeholder="Ej: Los Exploradores"
                        class="input-sala"
                    >
                    <span class="field-error" id="error-nombre-equipo"></span>
                </div>
            </div>
            <div class="sala-modal-footer">
                <button class="btn-primary" onclick="crearEquipo()">
                    <i class="bi bi-check-lg"></i> Crear
                </button>
                <button class="btn-ghost-small" onclick="cerrarModal('modalCrear')">Cancelar</button>
            </div>
        </div>
    </div>

    {{-- ── Toast ── --}}
    <div id="sala-toast" class="sala-toast" aria-live="polite"></div>

    @php
        $salaId    = $sala->id;
        $miEquipoId = $miEquipo ? $miEquipo->id : null;
    @endphp

    <script>
        window.salaConfig = {
            crearUrl:  '{{ route('sala.equipo.crear', $sala->id) }}',
            salirUrl:  '{{ route('sala.equipo.salir', $sala->id) }}',
            estadoLiveUrl: '{{ route('sala.estado.live', $sala->id) }}',
            mapaUrl: '{{ route('gimcana.mapa') }}',
            csrfToken: '{{ csrf_token() }}',
            salaId:    @json($salaId),
            miEquipoId: @json($miEquipoId),
            salaEstado: @json($sala->estado)
        };

        window.salaFlash = {
            success: @json(session('success')),
            error: @json(session('error'))
        };
    </script>
    <script src="{{ asset('js/sala/sala.js') }}"></script>

</body>
</html>
