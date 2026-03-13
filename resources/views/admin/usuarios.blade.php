<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Usuarios - GeoTurismo Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin_dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/usuarios.css') }}">
</head>
<body>
    <div class="admin-layout">
        @include('admin.admin_sidebar')

        <main class="main-content">
            <header class="dashboard-header">
                <div class="header-title">
                    <h1>Gestión de Usuarios</h1>
                    <p>Administra los usuarios de la plataforma y sus roles.</p>
                </div>

                <div class="header-actions">
                    <button class="btn-add-usuario" onclick="window.abrirModalCrear()">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <line x1="20" y1="8" x2="20" y2="14"></line>
                            <line x1="23" y1="11" x2="17" y2="11"></line>
                        </svg>
                        Añadir Nuevo Usuario
                    </button>
                    <span class="total-count-badge">Total de usuarios: <strong id="total-users">{{ $totalUsuarios }}</strong></span>
                </div>
            </header>

            <div class="filters-bar">
                <input type="text" id="filtroTexto" placeholder="Buscar por nombre o username..." onkeyup="window.filtrarUsuarios()">
                <select id="filtroRol" onchange="window.filtrarUsuarios()">
                    <option value="">Todos los roles</option>
                    <option value="1">Administrador</option>
                    <option value="2">Usuario Normal</option>
                </select>
            </div>

            <div class="table-container">
                <table class="usuarios-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usuarios-tbody">
                        <!-- Se llenará mediante AJAX (Fetch) -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Store routes to use in external JS files
        window.routes = {
            apiUsuarios: "{{ route('admin.api.usuarios') }}",
            apiStore: "{{ route('admin.api.usuarios.store') }}",
            apiUpdate: "{{ url('/admin/api/usuarios') }}",
            apiDelete: "{{ url('/admin/api/usuarios') }}"
        };
        // CSRF Token for fetch
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    
    <script src="{{ asset('js/admin/usuarios_filtrar.js') }}"></script>
    <script src="{{ asset('js/admin/usuarios_modals.js') }}"></script>
    <script src="{{ asset('js/admin/usuarios_crud.js') }}"></script>
    <script src="{{ asset('js/admin/usuarios_init.js') }}"></script>
</body>
</html>
