<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - GeoTurismo Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin_dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/categorias.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="admin-layout" id="categorias-app" data-base-url="{{ url('/admin/categorias') }}">
    @include('admin.admin_sidebar')

    <main class="main-content">

        <header class="categorias-header">
            <div class="header-title">
                <h1>Gestión de Categorías</h1>
                <p>Añade, edita y organiza las categorías de los lugares turísticos.</p>
            </div>
            <button class="btn-nueva-categoria" onclick="openCreate()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nueva Categoría
            </button>
        </header>

        @if(session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif

        @if($categorias->isEmpty())
            <div class="empty-categorias">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
                    <line x1="7" y1="7" x2="7.01" y2="7"/>
                </svg>
                <p>No hay categorías todavía. ¡Crea la primera!</p>
            </div>
        @else
            <div class="categorias-grid">
                @foreach($categorias as $cat)
                <div class="categoria-card">
                    <div class="categoria-icono" style="background-color: {{ $cat->color_marcador ?? '#e0f7f7' }}1a; color: {{ $cat->color_marcador ?? '#0ea5a4' }};">
                        @if($cat->icono_url)
                            <i class="{{ $cat->icono_url }}" style="font-size:1.5rem;"></i>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
                                <line x1="7" y1="7" x2="7.01" y2="7"/>
                            </svg>
                        @endif
                    </div>
                    <div class="categoria-info">
                        <p class="categoria-nombre">{{ $cat->nombre }}</p>
                        <p class="categoria-lugares">{{ $cat->lugares_count }} {{ $cat->lugares_count === 1 ? 'lugar' : 'lugares' }}</p>
                    </div>
                    <div class="categoria-actions">
                        <button class="btn-icon btn-edit"
                                title="Editar"
                                onclick="openEdit({{ $cat->id }}, '{{ addslashes($cat->nombre) }}', '{{ addslashes($cat->icono_url ?? '') }}', '{{ $cat->color_marcador ?? '#0ea5a4' }}')" >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <button class="btn-icon btn-delete"
                                title="Eliminar"
                                onclick="openDelete({{ $cat->id }}, '{{ addslashes($cat->nombre) }}')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                                <path d="M10 11v6M14 11v6"/>
                                <path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        @endif

    </main>
</div>

<!-- ── Modal Crear / Editar ── -->
<div id="modal-form" class="modal-overlay hidden">
    <div class="modal">
        <div class="modal-header">
            <h2 id="modal-form-title">Nueva Categoría</h2>
            <button class="btn-modal-close" onclick="closeModal('modal-form')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form id="form-categoria" method="POST">
            @csrf
            <span id="form-method-field"></span>

            <div class="modal-form-group">
                <label for="input-nombre">Nombre</label>
                <input type="text" id="input-nombre" name="nombre" placeholder="Ej. Museos" maxlength="100">
            </div>

            <div class="modal-form-group">
                <label for="input-icono">Icono Bootstrap <span style="font-weight:400;color:#94a3b8;">(opcional, ej: bi bi-geo-alt)</span></label>
                <div class="icono-preview-row">
                    <input type="text" id="input-icono" name="icono_url" placeholder="bi bi-tag" oninput="previewIcon(this.value)">
                    <div id="icono-preview" class="icono-preview-box"></div>
                </div>
            </div>

            <div class="modal-form-group">
                <label>Color del marcador</label>
                <div class="color-row">
                    <input type="color" id="input-color-picker" value="#0ea5a4" oninput="syncColor(this.value)">
                    <input type="text"  id="input-color-text"   name="color_marcador" value="#0ea5a4" maxlength="7"
                           placeholder="#0ea5a4" oninput="syncPicker(this.value)">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-form')">Cancelar</button>
                <button type="submit" class="btn-save">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- ── Modal Eliminar ── -->
<div id="modal-delete" class="modal-overlay hidden">
    <div class="modal">
        <div class="modal-header">
            <h2>Eliminar Categoría</h2>
            <button class="btn-modal-close" onclick="closeModal('modal-delete')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="modal-body-confirm">
            <p>¿Seguro que quieres eliminar <strong id="delete-nombre"></strong>?</p>
            <p style="color:#64748b;font-size:0.875rem;">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeModal('modal-delete')">Cancelar</button>
            <form id="form-delete" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirm-delete">Eliminar</button>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/admin/categoria/categorias.js') }}"></script>

</body>
</html>
