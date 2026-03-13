<div id="modal-add-template" style="display: none;">
    <form id="addForm" action="{{ route('admin.lugares.store') }}" method="POST" enctype="multipart/form-data" class="swal-form" style="text-align: left; font-family: 'Inter', sans-serif;">
        @csrf
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Nombre del Lugar</label>
            <input name="nombre" id="swal-add-nombre" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;" placeholder="Ej. Parc de la Torrassa" value="{{ old('nombre') }}" required>
            @error('nombre') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
        </div>
        
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Descripción</label>
            <textarea name="descripcion" id="swal-add-descripcion" class="swal2-textarea" style="width: 100%; margin: 0; min-height: 100px; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; padding: 0.75rem;" placeholder="Describe brevemente el lugar..." required>{{ old('descripcion') }}</textarea>
            @error('descripcion') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
        </div>
        
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Dirección Completa</label>
            <input name="direccion_completa" id="swal-add-direccion" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;" placeholder="Calle, número, ciudad..." value="{{ old('direccion_completa') }}" required>
            @error('direccion_completa') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Latitud</label>
                <input name="latitud" id="swal-add-latitud" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;" placeholder="41.36... " value="{{ old('latitud') }}" required>
                @error('latitud') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Longitud</label>
                <input name="longitud" id="swal-add-longitud" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;" placeholder="2.11... " value="{{ old('longitud') }}" required>
                @error('longitud') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
            </div>
        </div>
        
        <div style="margin-bottom: 0.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Categoría</label>
            <select name="id_categoria" id="swal-add-categoria" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;" required>
                <option value="" disabled {{ old('id_categoria') ? '' : 'selected' }}>Selecciona una categoría</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ old('id_categoria') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                @endforeach
            </select>
            @error('id_categoria') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
        </div>
 
        <div style="margin-top: 1.25rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Imagen del Banner</label>
            <input type="file" name="imagen" id="swal-add-imagen" class="swal2-file" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; font-size: 0.9rem;" accept="image/*">
            @error('imagen') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
        </div>
    </form>
</div>
