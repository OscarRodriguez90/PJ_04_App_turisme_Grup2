<div id="modal-edit-template-{{ $lugar->id }}" style="display: none;">
    <form id="editForm-{{ $lugar->id }}" action="{{ route('admin.lugares.update', $lugar->id) }}" method="POST" enctype="multipart/form-data" class="swal-form" style="text-align: left; font-family: 'Inter', sans-serif;">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" value="{{ $lugar->id }}">
        
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Nombre del Lugar</label>
            <input name="nombre" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;" placeholder="Ej. Parc de la Torrassa" value="{{ old('nombre', $lugar->nombre) }}" required>
            @if(old('id') == $lugar->id) @error('nombre') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror @endif
        </div>
        
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Descripción</label>
            <textarea name="descripcion" class="swal2-textarea" style="width: 100%; margin: 0; min-height: 100px; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; padding: 0.75rem;" placeholder="Describe brevemente el lugar..." required>{{ old('descripcion', $lugar->descripcion) }}</textarea>
            @if(old('id') == $lugar->id) @error('descripcion') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror @endif
        </div>
        
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Dirección Completa</label>
            <input name="direccion_completa" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;" placeholder="Calle, número, ciudad..." value="{{ old('direccion_completa', $lugar->direccion_completa) }}" required>
            @if(old('id') == $lugar->id) @error('direccion_completa') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror @endif
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Latitud</label>
                <input name="latitud" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;" value="{{ old('latitud', $lugar->latitud) }}" required>
                @if(old('id') == $lugar->id) @error('latitud') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror @endif
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Longitud</label>
                <input name="longitud" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;" value="{{ old('longitud', $lugar->longitud) }}" required>
                @if(old('id') == $lugar->id) @error('longitud') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror @endif
            </div>
        </div>
        
        <div style="margin-bottom: 1.0rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Categoría</label>
            <select name="id_categoria" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;" required>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ old('id_categoria', $lugar->id_categoria) == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                @endforeach
            </select>
            @if(old('id') == $lugar->id) @error('id_categoria') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror @endif
        </div>
 
        <div style="margin-top: 1.25rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Imagen del Banner</label>
            @if($lugar->imagen)
            <div style="margin-bottom: 0.75rem;">
                <img src="{{ asset('img/lugares/' . $lugar->imagen) }}" style="width: 100%; height: 120px; object-fit: cover; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
            </div>
            @endif
            <input type="file" name="imagen" class="swal2-file" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; font-size: 0.9rem;" accept="image/*">
            @if(old('id') == $lugar->id) @error('imagen') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror @endif
        </div>
    </form>
</div>
