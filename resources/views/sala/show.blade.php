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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div id="sala-dynamic-content">
        @include('sala.partials.dynamic_room')
    </div>

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
            unirseUrlTemplate: '/sala/{{ $sala->id }}/equipos/EQUIPO_ID/unirse',
            estadoLiveUrl: '{{ route('sala.estado.live', $sala->id) }}',
            mapaUrl: '{{ route('gimcana.mapa') }}',
            csrfToken: '{{ csrf_token() }}',
            salaId:    @json($salaId),
            miEquipoId: @json($miEquipoId),
            usuarioId: @json(auth()->id()),
            salaEstado: @json($sala->estado),
            retosCompletados: @json($retosCompletados ?? false),
            restartUrl: '{{ route('gimcana.reiniciar') }}'
        };

        window.salaFlash = {
            success: @json(session('success')),
            error: @json(session('error'))
        };
    </script>
    <script src="{{ asset('js/sala/sala.js') }}"></script>

</body>
</html>
