<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GeoTurismo Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin_dashboard.css') }}">
</head>
<body>
    <div class="admin-layout">
        @include('admin.admin_sidebar')

        <main class="main-content">
            <header class="dashboard-header">
                <div class="header-title">
                    <h1>Resumen General</h1>
                    <p>Gestiona lugares, categorías, usuarios y rutas desde un solo panel.</p>
                </div>
                
                <div class="user-profile">
                    <div class="user-info">
                        <span class="user-role">Administrador</span>
                        <span class="user-email">admin@geoturismo.com</span>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=0ea5a4&color=fff" alt="Avatar" class="user-avatar">
                </div>
            </header>

            <section class="stats-grid">
                <!-- Lugares -->
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value">{{ $stats['lugares'] }}</span>
                        <span class="stat-label">Lugares Registrados</span>
                    </div>
                </div>

                <!-- Categorías -->
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"></path>
                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value">{{ $stats['categorias'] }}</span>
                        <span class="stat-label">Categorías Activas</span>
                    </div>
                </div>

                <!-- Gimcanas -->
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 9a3 3 0 010 6v2a2 2 0 002 2h16a2 2 0 002-2v-2a3 3 0 010-6V7a2 2 0 00-2-2H4a2 2 0 00-2 2v2z"></path>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value">{{ $stats['gimcanas'] }}</span>
                        <span class="stat-label">Gimcanas Disponibles</span>
                    </div>
                </div>

                <!-- Usuarios -->
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value">{{ $stats['usuarios'] }}</span>
                        <span class="stat-label">Usuarios Totales</span>
                    </div>
                </div>
            </section>

            <section class="quick-actions">
                <h2>Acciones Rápidas</h2>
                <div class="actions-grid">
                    <a href="#" class="action-card">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        Añadir Nuevo Lugar
                    </a>
                    <a href="#" class="action-card">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        Crear Nueva Gimcana
                    </a>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
