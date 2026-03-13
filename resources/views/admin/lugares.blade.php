<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Lugares - GeoTurismo Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin_dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/lugares.css') }}">
</head>
<body>
    <div class="admin-layout">
        @include('admin.admin_sidebar')

        <main class="main-content">
            <header class="dashboard-header">
                <div class="header-title">
                    <h1>Gestión de Lugares</h1>
                    <p>Administra los puntos de interés turístico y sus categorías.</p>
                </div>

                @if(session('success'))
                    <input type="hidden" id="session-success-message" value="{{ session('success') }}">
                @endif
                <div class="header-actions">
                    <button class="btn-add-lugar" onclick="showAddModal()">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Añadir Nuevo Lugar
                    </button>
                    <span class="total-count-badge">Total de lugares: <strong>{{ $totalLugares }}</strong></span>
                </div>
            </header>

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
                <p style="margin: 0.5rem 0 0 0; font-size: 0.8rem; color: #4b5563;">El formulario se ha reabierto automáticamente para corregir los datos.</p>
            </div>

            {{-- Hidden signals for places_init.js --}}
            @if(old('id'))
                <input type="hidden" id="reopen-edit-id" value="{{ old('id') }}">
            @else
                <input type="hidden" id="reopen-add-flag" value="1">
            @endif
            @endif

            @foreach($lugaresGrouped as $categoryName => $lugares)
            <section class="category-section">
                <h2 class="category-title">
                    <span class="category-dot" style="background-color: {{ $lugares->first()->categoria->color_marcador ?? '#0ea5a4' }};"></span>
                    {{ $categoryName }}
                </h2>
                <div class="lugares-grid">
                    @foreach($lugares as $lugar)
                    <div class="lugar-card" 
                         style="cursor: pointer;"
                         data-id="{{ $lugar->id }}"
                         onclick="if(!event.target.closest('.lugar-actions')) handleCardClick({{ $lugar->id }}, event)">
                        
                        <div class="lugar-banner">
                            <img src="{{ asset('img/lugares/' . ($lugar->imagen ?? 'default_lugar.jpg')) }}" alt="{{ $lugar->nombre }}">
                        </div>

                        <div class="lugar-main">
                            <div class="lugar-title-wrapper">
                                <span class="status-dot" style="background-color: {{ $lugar->categoria->color_marcador ?? '#0ea5a4' }};"></span>
                                <h2 class="lugar-title">{{ $lugar->nombre }}</h2>
                            </div>
                            <div class="lugar-actions">
                                <button class="btn-action btn-edit" onclick="handleEditClick({{ $lugar->id }}, event)">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>
                                <button class="btn-action btn-delete" onclick="handleDeleteClick({{ $lugar->id }}, '{{ addslashes($lugar->nombre) }}', event)">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Hidden Templates for SweetAlert (PHP Population) --}}
                        @include('admin.partials.edit_lugar', ['lugar' => $lugar])
                        
                        <div id="modal-view-template-{{ $lugar->id }}" style="display: none;">
                            <div style="text-align: left; font-family: 'Inter', sans-serif; color: #475569; line-height: 1.6;">
                                <div style="margin-bottom: 1rem; padding: 1rem; background: #f8fafc; border-radius: 0.75rem; border-left: 4px solid {{ $lugar->categoria->color_marcador ?? '#0ea5a4' }}">
                                    <strong style="color: #1e293b; display: block; margin-bottom: 0.25rem;">Descripción</strong>
                                    <p style="margin: 0;">{{ $lugar->descripcion ?: 'Sin descripción disponible.' }}</p>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div style="background: #f1f5f9; padding: 0.75rem; border-radius: 0.5rem;">
                                        <strong style="color: #475569; font-size: 0.8rem; text-transform: uppercase;">Dirección</strong>
                                        <p style="margin: 0.25rem 0 0 0; font-weight: 500;">{{ $lugar->direccion_completa ?: 'N/A' }}</p>
                                    </div>
                                    <div style="background: #f1f5f9; padding: 0.75rem; border-radius: 0.5rem;">
                                        <strong style="color: #475569; font-size: 0.8rem; text-transform: uppercase;">Categoría</strong>
                                        <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: {{ $lugar->categoria->color_marcador ?? '#0ea5a4' }};">{{ $lugar->categoria->nombre }}</p>
                                    </div>
                                </div>

                                <div style="margin-top: 1rem; background: #f1f5f9; padding: 0.75rem; border-radius: 0.5rem;">
                                    <strong style="color: #475569; font-size: 0.8rem; text-transform: uppercase;">Coordenadas</strong>
                                    <p style="margin: 0.25rem 0 0 0; font-family: monospace;">Lat: {{ $lugar->latitud }}, Long: {{ $lugar->longitud }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endforeach
        </main>
    </div>

    <!-- Add Modal Template -->
    @include('admin.partials.add_lugar')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/admin/admin_notifications.js') }}"></script>
    <script src="{{ asset('js/admin/validaciones_lugares.js') }}"></script>
    <script src="{{ asset('js/admin/lugares_alerts.js') }}"></script>
    <script src="{{ asset('js/admin/lugares_init.js') }}"></script>
</body>
</html>
