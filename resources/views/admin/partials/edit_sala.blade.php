<div id="modal-edit-template-{{ $sala->id }}" style="display: none;">
    <form id="form-edit-sala-{{ $sala->id }}" action="{{ route('admin.salas.update', $sala->id) }}" method="POST" class="swal-form" style="text-align: left; font-family: 'Inter', sans-serif;">
        @csrf
        @method('PUT')
        <!-- Store the ID so JS can reference it -->
        <input type="hidden" name="id" value="{{ $sala->id }}">
        <div class="swal2-content-custom">
            
            <div style="margin-bottom: 1.25rem;">
                <label for="edit-codigo-{{ $sala->id }}" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #334155; font-size: 0.875rem;">Código de Sala (Máx. 8)</label>
                <input type="text" id="edit-codigo-{{ $sala->id }}" name="codigo_sala" class="swal2-input" required maxlength="8"
                       value="{{ old('codigo_sala', $sala->codigo_sala) }}"
                       oninput="this.value = this.value.toUpperCase(); validateField('edit-codigo-{{ $sala->id }}', 'codigo')"
                       onblur="validateField('edit-codigo-{{ $sala->id }}', 'codigo')"
                       style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem; font-family: monospace;">
                <span class="error-message" id="error-edit-codigo-{{ $sala->id }}" style="color: #ef4444; font-size: 0.75rem; display: none; margin-top: 0.25rem;"></span>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label for="edit-estado-{{ $sala->id }}" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Estado</label>
                <select id="edit-estado-{{ $sala->id }}" name="estado" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;">
                    <option value="esperando" {{ old('estado', $sala->estado) == 'esperando' ? 'selected' : '' }}>Esperando (Jugadores no iniciados)</option>
                    <option value="jugando" {{ old('estado', $sala->estado) == 'jugando' ? 'selected' : '' }}>Jugando (En curso)</option>
                    <option value="finalizada" {{ old('estado', $sala->estado) == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                </select>
            </div>

            <h3 style="margin-bottom: 1rem; color: #1e293b; font-size: 1.1rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem;">Configuración de Retos (5 Obligatorios)</h3>
            <span class="error-message" id="error-edit-lugares-total-{{ $sala->id }}" style="color: #ef4444; font-size: 0.9rem; display: none; margin-bottom: 1rem; font-weight:600;">Debes seleccionar exactamente 5 lugares distintos y rellenar completamentes sus retos y pistas.</span>

            <div id="edit-lugares-container-{{ $sala->id }}">
                @php
                    $pruebas = $sala->pruebas->sortBy('orden')->values();
                @endphp
                @for($i = 0; $i < 5; $i++)
                @php
                    $pruebaExistente = $pruebas->get($i);
                @endphp
                <div class="lugar-selector-container" id="edit-lugar-block-{{ $sala->id }}-{{ $i }}">
                    <h4 style="margin: 0 0 0.5rem 0; color: #475569;">Reto {{ $i + 1 }}</h4>
                    
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Lugar</label>
                        <input type="text" class="swal2-input search-lugar" placeholder="Escribe para buscar lugar..." onkeyup="filterLugar(this)" style="width: 100%; margin: 0 0 0.5rem 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem; display:block;">
                        <select name="lugares[{{$i}}][id_lugar]" class="lugar-select swal2-input" data-index="{{$i}}" onchange="validateFormEdit({{ $sala->id }})" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;">
                            <option value="">-- Selecciona un lugar --</option>
                            @foreach($lugaresSeleccionables as $lugar)
                                <option value="{{ $lugar->id }}" {{ old("lugares.$i.id_lugar", $pruebaExistente ? $pruebaExistente->id_lugar : '') == $lugar->id ? 'selected' : '' }}>{{ $lugar->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Pregunta / Reto</label>
                        <textarea name="lugares[{{$i}}][pregunta]" class="swal2-textarea" rows="2" placeholder="Ej: ¿Qué año se construyó esta catedral?" oninput="validateFormEdit({{ $sala->id }})" style="width: 100%; margin: 0; min-height: 80px; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; padding: 0.75rem;">{{ old("lugares.$i.pregunta", $pruebaExistente ? $pruebaExistente->pregunta : '') }}</textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Respuesta Correcta</label>
                            <input type="text" name="lugares[{{$i}}][respuesta_correcta]" class="swal2-input" placeholder="Ej: 1990" value="{{ old("lugares.$i.respuesta_correcta", $pruebaExistente ? $pruebaExistente->respuesta_correcta : '') }}" oninput="validateFormEdit({{ $sala->id }})" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Pista</label>
                            <textarea name="lugares[{{$i}}][pista]" class="swal2-textarea" rows="2" placeholder="Ej: Es la última en la década de los 90" oninput="validateFormEdit({{ $sala->id }})" style="width: 100%; margin: 0; min-height: 80px; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; padding: 0.75rem;">{{ old("lugares.$i.pista", $pruebaExistente ? $pruebaExistente->pista : '') }}</textarea>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </form>
</div>
