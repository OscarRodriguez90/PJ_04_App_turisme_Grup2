window.renderUsuarios = function(usuarios) {
    const tbody = document.getElementById('usuarios-tbody');
    tbody.innerHTML = '';
    
    document.getElementById('total-users').innerText = usuarios.length;

    if(usuarios.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #64748b; padding: 2rem; font-family: Inter, sans-serif;">No se encontraron usuarios.</td></tr>';
        return;
    }

    // Keep reference of current users globally to find them later for editing without another fetch
    window.currentUsuarios = usuarios;

    let html = '';
    for (let i = 0; i < usuarios.length; i++) {
        const u = usuarios[i];
        let roleBadge = (u.id_rol == 1) ? 'Administrador' : 'Usuario Normal';
        let roleClass = (u.id_rol == 1) ? 'style="background-color: #fef2f2; color: #ef4444;"' : '';
        let fotoUrl = u.foto ? '/img/usuarios/' + u.foto : '/img/usuarios/default_user.png';
        
        html += `
            <tr>
                <td>
                    <div class="usuario-cell">
                        <img src="${fotoUrl}" alt="Avatar" class="td-avatar" onerror="this.src='/img/usuarios/default_user.png'">
                        <div>
                            <div class="td-name">${u.nombre} ${u.apellido1} ${u.apellido2 || ''}</div>
                            <div class="td-username">@${u.username}</div>
                        </div>
                    </div>
                </td>
                <td class="td-email">${u.email}</td>
                <td><span class="usuario-role" ${roleClass}>${roleBadge}</span></td>
                <td>
                    <div class="td-actions">
                        <button class="btn-action btn-edit" title="Editar" onclick="window.abrirModalEditar(${u.id})">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>
                        <button class="btn-action btn-delete" title="Eliminar" onclick="window.eliminarUsuario(${u.id}, '${u.nombre}')">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }
    tbody.innerHTML = html;
};

window.onload = function() {
    window.filtrarUsuarios();
};
