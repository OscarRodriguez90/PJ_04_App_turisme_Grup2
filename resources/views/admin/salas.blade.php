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
    <style>
        .gimcana-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            position: relative;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .gimcana-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .gimcana-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .gimcana-code {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            font-family: monospace;
            background: #f1f5f9;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
        }

        .gimcana-status {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            text-transform: uppercase;
        }
        .status-esperando { background: #fef3c7; color: #92400e; }
        .status-jugando { background: #dbeafe; color: #1e40af; }
        .status-finalizada { background: #d1fae5; color: #065f46; }

        .lugar-list {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 0.85rem;
            color: #475569;
        }
        
        .lugar-list li {
            padding: 0.25rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .lugar-list li:last-child {
            border-bottom: none;
        }

        .lugar-selector-container {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #f8fafc;
        }
        .lugar-selector-container.error-border {
            border-color: #ef4444;
            background: #fef2f2;
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        @include('admin.admin_sidebar')

        <main class="main-content">
            <header class="dashboard-header">
                <div class="header-title">
                    <h1>Gestión de Gimcanas</h1>
                    <p>Crea y administra las salas y sus retos de geolocalización.</p>
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
                        Crear Gimcana
                    </button>
                    <span class="total-count-badge">Total de salas: <strong>{{ $totalSalas }}</strong></span>
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
                            <span class="gimcana-code">{{ $sala->codigo_sala }}</span>
                            <span class="gimcana-status status-{{ $sala->estado }}">{{ $sala->estado }}</span>
                        </div>
                        
                        <div>
                            <strong style="font-size: 0.8rem; color: #64748b; text-transform: uppercase;">Lugares Asignados ({{ $sala->pruebas->count() }}/5)</strong>
                            <ul class="lugar-list" style="margin-top: 0.5rem;">
                                @foreach($sala->pruebas->sortBy('orden') as $prueba)
                                <li>{{ $prueba->orden }}. {{ $prueba->lugar->nombre ?? 'Lugar Desconocido' }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="lugar-actions" style="position: static; opacity: 1; transform: none; display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: auto;">
                            <button class="btn-action btn-edit" onclick="handleEditClick({{ $sala->id }})" title="Editar Gimcana">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </button>
                            <button class="btn-action btn-delete" onclick="handleDeleteClick({{ $sala->id }}, '{{ $sala->codigo_sala }}')" title="Eliminar Gimcana">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </button>
                        </div>

                        {{-- Hidden views for modal population --}}
                        @include('admin.partials.edit_sala', ['sala' => $sala, 'lugaresSeleccionables' => $lugares])
                    </div>
                    @endforeach
                </div>
            </section>
        </main>
    </div>

    <!-- Add Modal Template -->
    @include('admin.partials.add_sala', ['lugaresSeleccionables' => $lugares])

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/admin/admin_notifications.js') }}"></script>
    <script src="{{ asset('js/admin/validaciones_salas.js') }}"></script>
    <script src="{{ asset('js/admin/salas_alerts.js') }}"></script>
    <script src="{{ asset('js/admin/salas_init.js') }}"></script>
</body>
</html>
