window.getFormHtml = function(isEdit, user = {}) {
    let pRequire = isEdit ? '' : 'required';
    let pHelp = isEdit ? '<small style="color:#64748b">Déjalo en blanco para mantener la actual.</small>' : '';
    
    return `
        <form id="usuarioForm" class="swal-form" style="display:flex; flex-direction:column; gap:0.5rem; text-align:left;">
            <div class="form-group">
                <label>Nombre de Usuario *</label>
                <input type="text" id="swal-username" class="swal2-input" style="margin:0; width:100%;" value="${user.username || ''}" required>
            </div>
            <div class="form-group" style="display:grid; grid-template-columns: 1fr 1fr; gap:0.5rem;">
                <div>
                    <label>Nombre *</label>
                    <input type="text" id="swal-nombre" class="swal2-input" style="margin:0; width:100%;" value="${user.nombre || ''}" required>
                </div>
                <div>
                    <label>Primer Apellido *</label>
                    <input type="text" id="swal-apellido1" class="swal2-input" style="margin:0; width:100%;" value="${user.apellido1 || ''}" required>
                </div>
            </div>
            <div class="form-group">
                <label>Segundo Apellido</label>
                <input type="text" id="swal-apellido2" class="swal2-input" style="margin:0; width:100%;" value="${user.apellido2 || ''}">
            </div>
            <div class="form-group" style="display:grid; grid-template-columns: 1fr 1fr; gap:0.5rem;">
                <div>
                    <label>Email *</label>
                    <input type="email" id="swal-email" class="swal2-input" style="margin:0; width:100%;" value="${user.email || ''}" required>
                </div>
                <div>
                    <label>Contraseña ${isEdit ? '' : '*'}</label>
                    <input type="password" id="swal-password" class="swal2-input" style="margin:0; width:100%;">
                    ${pHelp}
                </div>
            </div>
            <div class="form-group" style="display:grid; grid-template-columns: 1fr 1fr; gap:0.5rem;">
                <div>
                    <label>Rol *</label>
                    <select id="swal-rol" class="swal2-input" style="margin:0; width:100%; height:45px;" required>
                        <option value="1" ${user.id_rol == 1 ? 'selected' : ''}>Administrador</option>
                        <option value="2" ${user.id_rol == 2 || !user.id_rol ? 'selected' : ''}>Usuario Normal</option>
                    </select>
                </div>
                <div>
                    <label>Foto Perfil</label>
                    <input type="file" id="swal-foto" class="swal2-file" style="margin:0; width:100%; padding-top:0.5rem;" accept="image/*">
                </div>
            </div>
        </form>
    `;
};

window.abrirModalCrear = function() {
    Swal.fire({
        title: 'Añadir Nuevo Usuario',
        html: window.getFormHtml(false),
        showCancelButton: true,
        confirmButtonText: 'Guardar Usuario',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'premium-swal-popup',
            confirmButton: 'premium-swal-button btn-add-usuario',
            cancelButton: 'premium-swal-button'
        },
        width: '600px',
        preConfirm: function() {
            return window.obtenerDatosFormulario();
        }
    }).then(function(result) {
        if (result.isConfirmed) {
            window.guardarUsuario(result.value);
        }
    });
};

window.abrirModalEditar = function(id) {
    if(!window.currentUsuarios) return;

    let user = null;
    for(let i=0; i<window.currentUsuarios.length; i++) {
        if(window.currentUsuarios[i].id == id) {
            user = window.currentUsuarios[i];
            break;
        }
    }
    
    if(!user) return;

    Swal.fire({
        title: 'Editar Usuario',
        html: window.getFormHtml(true, user),
        showCancelButton: true,
        confirmButtonText: 'Actualizar Usuario',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'premium-swal-popup',
            confirmButton: 'premium-swal-button btn-add-usuario',
            cancelButton: 'premium-swal-button'
        },
        width: '600px',
        preConfirm: function() {
            let data = window.obtenerDatosFormulario();
            data.append('_method', 'PUT'); // Fake PUT for Laravel
            return { id: id, data: data };
        }
    }).then(function(result) {
        if (result.isConfirmed) {
            window.actualizarUsuario(result.value.id, result.value.data);
        }
    });
};

window.obtenerDatosFormulario = function() {
    let formData = new FormData();
    formData.append('username', document.getElementById('swal-username').value);
    formData.append('nombre', document.getElementById('swal-nombre').value);
    formData.append('apellido1', document.getElementById('swal-apellido1').value);
    formData.append('apellido2', document.getElementById('swal-apellido2').value);
    formData.append('email', document.getElementById('swal-email').value);
    formData.append('id_rol', document.getElementById('swal-rol').value);
    
    let pass = document.getElementById('swal-password').value;
    if(pass) formData.append('password', pass);
    
    let foto = document.getElementById('swal-foto').files[0];
    if(foto) formData.append('foto', foto);

    return formData;
};
