<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crear Gimcana - GeoTurismo Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin_dashboard.css') }}">
    <!-- Tailwind-like utilities mimicking the modal styles -->
    <link rel="stylesheet" href="{{ asset('css/admin/salas.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="admin-layout">
        @include('admin.admin_sidebar')

        <main class="main-content">
            <header class="dashboard-header" style="margin-bottom: 2rem;">
                <div class="header-title">
                    <h1>Gestión de Gimcanas</h1>
                    <p>Crea una nueva sala interactiva y confígura sus retos.</p>
                </div>
            </header>


            <div class="form-container">
                <div class="header-section">
                    <h2>Nueva Gimcana</h2>
                    <a href="{{ route('admin.salas') }}" class="btn-back">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Volver
                    </a>
                </div>

                <form id="form-add-sala" action="{{ route('admin.salas.store') }}" method="POST" onsubmit="return validateFormAdd(event)">
                    @csrf
                    
                    <div class="form-group">
                        <label for="add-nombre" class="form-label">Nombre de la Gimcana</label>
                        <input type="text" id="add-nombre" name="nombre" class="form-input" maxlength="50"
                               value="{{ old('nombre') }}"
                               oninput="validateField('add-nombre', 'nombre')"
                               onblur="validateField('add-nombre', 'nombre')"
                               placeholder="Ej: Gimcana del Barrio Gòtic">
                        <span class="error-message-text" id="error-add-nombre"></span>
                    </div>

                    <div class="form-group">
                        <label for="add-descripcion" class="form-label">Descripción <span style="color:#94a3b8;font-weight:400">(opcional)</span></label>
                        <textarea id="add-descripcion" name="descripcion" class="form-textarea" rows="2"
                                  maxlength="255" style="min-height:70px;"
                                  oninput="validateField('add-descripcion', 'descripcion')"
                                  placeholder="Breve descripción de la gimcana...">{{ old('descripcion') }}</textarea>
                        <span class="error-message-text" id="error-add-descripcion"></span>
                    </div>

                    <h3 style="margin: 2rem 0 1rem 0; color: #1e293b; font-size: 1.25rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem;">Configuración de Retos (5 Obligatorios)</h3>
                    <span class="error-message-text" id="error-add-lugares-total" style="font-size: 0.9rem; margin-bottom: 1rem; font-weight:600;">Debes seleccionar exactamente 5 lugares distintos y rellenar completamentes sus retos y pistas.</span>

                    <div id="add-lugares-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 1.5rem;">
                        @for($i = 0; $i < 5; $i++)
                        <div class="lugar-selector-container" id="add-lugar-block-{{ $i }}" style="margin-bottom: 0;">
                            <h4 style="margin: 0 0 1rem 0; color: #475569; font-size: 1.1rem;">Reto {{ $i + 1 }}</h4>
                            
                            <div class="custom-select-container" id="add-lugar-block-{{ $i }}">
                                <label class="form-label">Lugar</label>
                                <input type="hidden" name="lugares[{{$i}}][id_lugar]" class="hidden-lugar-id" value="{{ old("lugares.$i.id_lugar") }}">
                                @php
                                    $oldLugarId = old("lugares.$i.id_lugar");
                                    $oldLugarNombre = '';
                                    if($oldLugarId) {
                                        $foundLugar = $lugaresSeleccionables->firstWhere('id', $oldLugarId);
                                        if($foundLugar) $oldLugarNombre = $foundLugar->nombre;
                                    }
                                @endphp
                                <input type="text" class="search-lugar-input" placeholder="Buscar lugar por nombre..." value="{{ $oldLugarNombre }}" autocomplete="off">
                                <svg class="search-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                
                                <div class="custom-select-dropdown">
                                    @foreach($lugaresSeleccionables as $lugar)
                                        <div class="custom-option {{ $oldLugarId == $lugar->id ? 'selected' : '' }}" data-value="{{ $lugar->id }}">
                                            {{ $lugar->nombre }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Pregunta / Reto</label>
                                <textarea name="lugares[{{$i}}][pregunta]" class="form-textarea" rows="2" placeholder="Ej: ¿Qué año se construyó esta catedral?" style="min-height: 80px;">{{ old("lugares.$i.pregunta") }}</textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Respuesta Correcta</label>
                                <input type="text" name="lugares[{{$i}}][respuesta_correcta]" class="form-input" placeholder="Ej: 1990" value="{{ old("lugares.$i.respuesta_correcta") }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Pista</label>
                                <textarea name="lugares[{{$i}}][pista]" class="form-textarea" rows="2" placeholder="Ej: Es la última en la década de los 90" style="min-height: 80px;">{{ old("lugares.$i.pista") }}</textarea>
                            </div>
                        </div>
                        @endfor
                    </div>

                    @if($errors->any())
                    <div id="php-error-box" style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg style="color: #ef4444; margin-right: 0.5rem;" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <strong style="color: #991b1b;">¡Error del servidor!</strong>
                        </div>
                        <ul style="margin: 0; padding-left: 1.5rem; color: #b91c1c; font-size: 0.9rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <button type="submit" class="btn-submit">
                        Guardar Gimcana
                    </button>
                </form>
            </div>
        </main>
    </div>

    <script src="{{ asset('js/admin/validaciones_salas.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const errorBox = document.getElementById('php-error-box');
            if (errorBox) {
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
</body>
</html>
