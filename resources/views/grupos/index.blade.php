<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Grupos - GeoTurismo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/grupos/grupos.css') }}">
</head>
<body>
    <header class="topbar">
        <a href="{{ $salaId ? route('sala.show', $salaId) : route('cliente.index') }}" class="btn-link">
            <i class="bi bi-arrow-left"></i>
            Volver
        </a>
        <h1>{{ $salaId ? 'Grupos de Sala' : 'Grupos' }}</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-link">Salir</button>
        </form>
    </header>

    <main class="container">
        @if($miEquipo)
            <section class="card card-current">
                <div class="card-head">
                    <h2>Tu grupo</h2>
                    <span class="badge">#{{ str_pad((string) $miEquipo->numero_equipo, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h3 class="group-name">{{ $miEquipo->nombre_equipo }}</h3>
                <p class="group-meta">Integrantes: {{ $miEquipo->integrantes->count() }}</p>
                <button class="btn btn-danger" onclick="salirGrupo()">Salir del grupo</button>
            </section>
        @else
            <section class="card">
                <div class="card-head">
                    <h2>Sin grupo</h2>
                </div>
                <p class="group-meta">Crea un grupo o unete a uno existente.</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem">
                    <button class="btn" onclick="abrirModalCrear()">
                        <i class="bi bi-plus-circle"></i>
                        Crear
                    </button>
                    <button class="btn" style="background:#6366f1" onclick="abrirModalCodigo()">
                        <i class="bi bi-key"></i>
                        Código
                    </button>
                </div>
            </section>
        @endif

        <section class="card">
            <div class="card-head">
                <h2>Vista de prueba</h2>
            </div>
            <p class="group-meta">Pantalla demo para visualizar una pregunta de la gimcana (sin funcionalidad).</p>
            <a href="{{ route('gimcana.pregunta') }}" class="btn" style="text-decoration:none">
                <i class="bi bi-ticket-perforated"></i>
                Abrir pregunta demo
            </a>
        </section>

        <section class="section-list">
            <div class="section-row">
                <h2>Grupos disponibles</h2>
                <span class="small-badge">{{ $equipos->count() }}</span>
            </div>

            @forelse($equipos as $equipo)
                @php $esMio = $miEquipo && $miEquipo->id === $equipo->id; @endphp
                <article class="group-card {{ $esMio ? 'is-mine' : '' }}">
                    <div>
                        <strong>{{ $equipo->nombre_equipo }}</strong>
                        <p>Codigo: #{{ str_pad((string) $equipo->numero_equipo, 6, '0', STR_PAD_LEFT) }}</p>
                        <p>Lider: {{ $equipo->lider->nombre ?? 'Sin lider' }}</p>
                        <p>Miembros: {{ $equipo->integrantes_count }}</p>
                    </div>
                    @if($esMio)
                        <span class="mine-pill">Tu grupo</span>
                    @elseif(!$miEquipo)
                        <button class="btn btn-outline" onclick="unirseGrupo({{ $equipo->id }})">Unirme</button>
                    @endif
                </article>
            @empty
                <article class="group-card empty">
                    <p>No hay grupos todavia.</p>
                </article>
            @endforelse
        </section>
    </main>

    <div class="modal-overlay" id="modalCrear" hidden>
        <div class="modal">
            <div class="modal-head">
                <h3>Crear grupo</h3>
                <button class="btn-icon" onclick="cerrarModal('modalCrear')"><i class="bi bi-x-lg"></i></button>
            </div>
            <label for="input-nombre-grupo">Nombre del grupo</label>
            <input id="input-nombre-grupo" type="text" maxlength="50" placeholder="Ej: Aventureros" class="input">
            <span class="field-error" id="error-nombre-grupo"></span>
            <button class="btn" onclick="crearGrupo()">Crear</button>
        </div>
    </div>

    <div class="modal-overlay" id="modalCodigo" hidden>
        <div class="modal">
            <div class="modal-head">
                <h3>Unirse con código</h3>
                <button class="btn-icon" onclick="cerrarModal('modalCodigo')"><i class="bi bi-x-lg"></i></button>
            </div>
            <label for="input-codigo-grupo">Código del grupo</label>
            <input id="input-codigo-grupo" type="text" maxlength="6" placeholder="000000" class="input" inputmode="numeric" style="letter-spacing:.1em;text-transform:uppercase">
            <span class="field-error" id="error-codigo-grupo"></span>
            <button class="btn" onclick="unirseConCodigo()">Unirse</button>
        </div>
    </div>

    <div class="toast" id="toast"></div>

    <script>
        window.gruposConfig = {
            storeUrl: '{{ $salaId ? route('sala.grupos.store', $salaId) : route('grupos.store') }}',
            leaveUrl: '{{ $salaId ? route('sala.grupos.leave', $salaId) : route('grupos.leave') }}',
            joinByCodeUrl: '{{ $salaId ? route('sala.grupos.joinByCode', $salaId) : route('grupos.joinByCode') }}',
            joinBaseUrl: '{{ $salaId ? route('sala.grupos.index', $salaId) : url('/grupos') }}',
            csrfToken: '{{ csrf_token() }}',
            salaId: @json($salaId)
        };
    </script>
    <script src="{{ asset('js/grupos/grupos.js') }}"></script>
</body>
</html>
