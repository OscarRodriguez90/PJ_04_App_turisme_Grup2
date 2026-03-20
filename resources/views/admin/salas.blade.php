<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Gimcanas - GeoTurismo Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin_dashboard.css') }}">
    <!-- Reusing lugares css for grid layout, and specific one for salas -->
    <link rel="stylesheet" href="{{ asset('css/admin/lugares.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/salas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/responsive.css') }}">
</head>
<body>
    <div class="admin-layout">
        @include('admin.admin_sidebar')

        @if(session('success'))
            <input type="hidden" id="session-success-message" value="{{ session('success') }}">
        @endif
        @if(session('error'))
            <input type="hidden" id="session-error-message" value="{{ session('error') }}">
        @endif

        <main class="main-content">
            @include('admin.partials.admin_header', [
                'title' => 'Gestión de Gimcanas',
                'subtitle' => 'Crea y administra las salas y sus retos de geolocalización.',
                'user' => $user
            ])

            <div class="header-actions" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <a href="{{ route('admin.salas.create') }}" class="btn-add-lugar" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Crear Gimcana
                </a>
                <span class="total-count-badge">Total de salas: <strong>{{ $totalSalas }}</strong></span>
            </div>

            {{-- PHP-Driven Error Alert --}}
            @if($errors->any())
            <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-family: 'Inter', sans-serif;">
                <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                    <svg style="color: #ef4444; margin-right: 0.5rem;" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <strong style="color: #991b1b;">¡Error de validación!</strong>
                </div>
                <ul style="margin: 0; padding-left: 1.5rem; color: #b91c1c; font-size: 0.9rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

            @if(old('id'))
                <input type="hidden" id="reopen-edit-id" value="{{ old('id') }}">
            @else
                <input type="hidden" id="reopen-add-flag" value="1">
            @endif
            @endif

            <section class="category-section">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                    @foreach($salas as $sala)
                    <div class="gimcana-card" data-id="{{ $sala->id }}">
                        <div class="gimcana-header">
                            <span class="gimcana-code">{{ $sala->nombre }}</span>
                        </div>

                        <div class="gimcana-estado-row">
                            <label class="estado-label">Estado:</label>
                            <span class="estado-badge estado-{{ $sala->estado }}">{{ ucfirst($sala->estado) }}</span>
                        </div>

                        @if($sala->descripcion)
                        <p style="margin: 0; font-size: 0.875rem; color: #64748b;">{{ $sala->descripcion }}</p>
                        @endif

                        <div>
                            <strong style="font-size: 0.8rem; color: #64748b; text-transform: uppercase;">Lugares Asignados ({{ $sala->pruebas->count() }}/5)</strong>
                            <ul class="lugar-list" style="margin-top: 0.5rem;">
                                @foreach($sala->pruebas->sortBy('orden') as $prueba)
                                <li>{{ $prueba->orden }}. {{ $prueba->lugar->nombre ?? 'Lugar Desconocido' }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="grupos-live-block" data-sala-id="{{ $sala->id }}">
                            <strong class="grupos-live-title">Grupos en la sala (en vivo)</strong>
                            <ul class="grupos-live-list js-grupos-live-list">
                                @forelse($sala->equipos as $equipo)
                                    @php
                                        $count = (int) $equipo->integrantes_count;
                                        $statusClass = $count < 2 ? 'grupo-count--warn' : ($count >= 8 ? 'grupo-count--full' : '');
                                    @endphp
                                    <li>
                                        <span>{{ $equipo->nombre_equipo }}</span>
                                        <span class="grupo-count {{ $statusClass }}">
                                            {{ $count }}/8
                                            @if($count < 2)
                                                incompleto
                                            @elseif($count >= 8)
                                                lleno
                                            @else
                                                jugadores
                                            @endif
                                        </span>
                                    </li>
                                @empty
                                    <li class="grupos-empty">Sin grupos en esta sala</li>
                                @endforelse
                            </ul>
                        </div>

                        <div class="lugar-actions" style="position: static; opacity: 1; transform: none; display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: auto;">
                            @if($sala->estado !== 'jugando')
                            <button
                                type="button"
                                class="btn-action btn-start"
                                onclick="empezarGimcana(this, {{ $sala->id }})"
                                title="Empezar Gimcana"
                            >
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                </svg>
                            </button>
                            @endif
                            @if($sala->estado === 'jugando')
                            <button
                                type="button"
                                class="btn-action btn-reset"
                                onclick="reiniciarPartida(this, {{ $sala->id }})"
                                title="Reiniciar partida"
                            >
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="1 4 1 10 7 10"></polyline>
                                    <path d="M3.51 15a9 9 0 1 0 .49-9.5L1 10"></path>
                                </svg>
                            </button>
                            @endif
                            <a href="{{ route('admin.salas.edit', $sala->id) }}" class="btn-action btn-edit" title="Editar Gimcana" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>
                            <button class="btn-action btn-delete" onclick="handleDeleteClick({{ $sala->id }}, '{{ $sala->nombre }}')" title="Eliminar Gimcana">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </button>
                        </div>


                    </div>
                    @endforeach
                </div>
            </section>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/admin/responsive.js') }}"></script>
    <script src="{{ asset('js/admin/admin_notifications.js') }}"></script>
    <script src="{{ asset('js/admin/validaciones_salas.js') }}"></script>
    <script src="{{ asset('js/admin/salas_alerts.js') }}"></script>
    <script>
        window.adminSalasConfig = {
            liveGruposUrl: '{{ route('admin.salas.liveGrupos') }}',
            updateEstadoBaseUrl: '{{ url('/admin/salas') }}'
        };
    </script>
    <script src="{{ asset('js/admin/salas_live.js') }}"></script>
</body>
</html>
