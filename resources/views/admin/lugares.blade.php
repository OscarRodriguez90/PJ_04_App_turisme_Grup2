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
            <header class="lugares-header">
                <div class="header-title">
                    <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0; color: #0f172a;">Gestión de Lugares</h1>
                    <p style="color: #64748b; margin: 0; font-size: 1rem;">Administra los puntos de interés y sus detalles en el mapa.</p>
                </div>
                <div class="header-actions">
                    <button id="btn-add-lugar" class="btn-add-lugar" onclick="showAddModal()">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Añadir Lugar
                    </button>
                    <span class="total-count-badge">Total de lugares: <strong>{{ $totalLugares }}</strong></span>
                </div>
            </header>

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
                         data-nombre="{{ $lugar->nombre }}"
                         data-descripcion="{{ $lugar->descripcion }}"
                         data-direccion="{{ $lugar->direccion_completa }}"
                         data-latitud="{{ $lugar->latitud }}"
                         data-longitud="{{ $lugar->longitud }}"
                         data-id-categoria="{{ $lugar->id_categoria }}"
                         data-categoria-nombre="{{ $lugar->categoria->nombre }}"
                         data-color="{{ $lugar->categoria->color_marcador }}"
                         onclick="if(!event.target.closest('.lugar-actions')) handleCardClick(this)">
                        <div class="lugar-main">
                            <div class="lugar-title-wrapper">
                                <span class="status-dot" style="background-color: {{ $lugar->categoria->color_marcador ?? '#0ea5a4' }};"></span>
                                <h2 class="lugar-title">{{ $lugar->nombre }}</h2>
                            </div>
                            <div class="lugar-actions">
                                <button class="btn-action btn-edit" onclick="handleEditClick(this, event)">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>
                                <button class="btn-action btn-delete" onclick="handleDeleteClick(this, event)">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endforeach
        </main>
    </div>

    <!-- Modals Templates -->
    @include('admin.partials.edit_lugar')
    @include('admin.partials.add_lugar')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#0ea5a4',
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'premium-swal-popup',
                confirmButton: 'premium-swal-button'
            }
        });
    </script>
    @endif

    @if($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: '¡Error de validación!',
            html: `
                <ul style="text-align: left; list-style: none; padding: 0; margin: 0; font-family: 'Inter', sans-serif;">
                    @foreach($errors->all() as $error)
                        <li style="margin-bottom: 0.5rem; color: #ef4444;">• {{ $error }}</li>
                    @endforeach
                </ul>
            `,
            confirmButtonColor: '#0ea5a4',
            customClass: {
                popup: 'premium-swal-popup',
                confirmButton: 'premium-swal-button'
            }
        });
    </script>
    @endif
    <script src="{{ asset('js/admin/lugares_alerts.js') }}"></script>
</body>
</html>
