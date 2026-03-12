<div id="modal-add-template" style="display: none;">
    <form id="addForm" class="swal-form" style="text-align: left; font-family: 'Inter', sans-serif;">
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Nombre del Lugar</label>
            <input id="swal-add-nombre" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;" placeholder="Ej. Parc de la Torrassa">
        </div>
        
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Descripción</label>
            <textarea id="swal-add-descripcion" class="swal2-textarea" style="width: 100%; margin: 0; min-height: 100px; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; padding: 0.75rem;" placeholder="Describe brevemente el lugar..."></textarea>
        </div>
        
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Dirección Completa</label>
            <input id="swal-add-direccion" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;" placeholder="Calle, número, ciudad...">
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Latitud</label>
                <input id="swal-add-latitud" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;" placeholder="41.36... ">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Longitud</label>
                <input id="swal-add-longitud" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;" placeholder="2.11... ">
            </div>
        </div>
        
        <div style="margin-bottom: 0.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; font-size: 0.875rem;">Categoría</label>
            <select id="swal-add-categoria" class="swal2-input" style="width: 100%; margin: 0; font-family: 'Inter', sans-serif; border-radius: 0.5rem; font-size: 0.9rem; height: 2.5rem;">
                <option value="" disabled selected>Selecciona una categoría</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>
