(function () {
    'use strict';

    const config = window.salaConfig;

    // ── HTTP helper ──────────────────────────────────────────
    async function postJson(url, data) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': config.csrfToken,
            },
            body: JSON.stringify(data),
        });

        let payload = {};
        try {
            payload = await res.json();
        } catch (_) {
            payload = {};
        }

        return { status: res.status, data: payload };
    }

    // ── Toast ────────────────────────────────────────────────
    let toastTimer = null;

    function mostrarNotificacion(msg, esError) {
        const toast = document.getElementById('sala-toast');
        if (!toast) return;
        toast.textContent = msg;
        toast.classList.toggle('is-error', !!esError);
        toast.classList.add('is-visible');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('is-visible'), 3000);
    }

    function reloadSoon() {
        setTimeout(() => location.reload(), 600);
    }

    function mostrarFlashServidorSiExiste() {
        if (!window.salaFlash) return;

        if (window.salaFlash.success) {
            mostrarNotificacion(window.salaFlash.success, false);
            window.salaFlash.success = null;
            return;
        }

        if (window.salaFlash.error) {
            mostrarNotificacion(window.salaFlash.error, true);
            window.salaFlash.error = null;
        }
    }

    async function checkEstado() {
        if (!config.estadoLiveUrl) return;

        try {
            const res = await fetch(config.estadoLiveUrl, {
                headers: { Accept: 'application/json' },
            });

            if (!res.ok) return;

            const payload = await res.json();
            
            // 1. Redirigir si la partida ha empezado
            if (payload.estado === 'jugando' && payload.miEquipoId) {
                window.location.href = config.mapaUrl;
                return;
            }

            // 2. Actualizar UI dinámicamente
            renderRoom(payload);
            
            // Actualizar config local para futuras acciones
            config.miEquipoId = payload.miEquipoId;
            config.salaEstado = payload.estado;

        } catch (e) {
            console.error("Error polling room status:", e);
        }
    }

    function renderRoom(data) {
        const container = document.getElementById('sala-dynamic-content');
        if (!container) return;

        let html = '';

        // HEADER
        html += `
            <header class="sala-header">
                <div class="sala-header-info">
                    <span class="sala-code-badge">${data.nombre}</span>
                    <span class="sala-estado sala-estado--${data.estado}">${data.estado.charAt(0).toUpperCase() + data.estado.slice(1)}</span>
                </div>
                <div class="sala-header-user">
                    <span class="user-name-header"><i class="bi bi-person-circle"></i> ${data.usuarioNombre}</span>
                    <a href="/sala" class="btn-ghost-small" id="btn-leave-room-dynamic">
                        <i class="bi bi-box-arrow-left"></i> Salir
                    </a>
                </div>
            </header>
        `;

        // MAIN
        html += '<main class="sala-main">';

        // MI EQUIPO SECTION
        if (data.miEquipo) {
            html += `
                <section class="sala-section">
                    <h2 class="section-heading"><i class="bi bi-people-fill"></i> Mi equipo</h2>
                    <div class="equipo-card equipo-card--mine">
                        <div class="equipo-card-header">
                            <span class="equipo-nombre">${data.miEquipo.nombre_equipo}</span>
                            <span class="equipo-count"><i class="bi bi-person"></i> ${data.miEquipo.integrantes.length}</span>
                        </div>
                        <div class="equipo-miembros">
                            ${data.miEquipo.integrantes.map(m => `
                                <div class="miembro-chip">
                                    <img src="${m.foto}" alt="Foto" class="miembro-avatar" onerror="this.src='/img/usuarios/default_user.png'">
                                    <span>${m.nombre}</span>
                                    ${m.id === data.miEquipo.id_lider ? '<i class="bi bi-star-fill lider-icon"></i>' : ''}
                                </div>
                            `).join('')}
                        </div>
                        <button class="btn-danger-small" onclick="salirEquipo()">
                            <i class="bi bi-door-open"></i> Salir del equipo
                        </button>
                        ${config.retosCompletados ? `
                            <form method="POST" action="${config.restartUrl}" class="restart-retos-form">
                                <input type="hidden" name="_token" value="${config.csrfToken}">
                                <button type="submit" class="btn-outline-small btn-start-retos">
                                    <i class="bi bi-arrow-counterclockwise"></i> Volver a empezar retos
                                </button>
                            </form>
                        ` : ''}
                    </div>
                </section>
            `;
        } else {
            html += `
                <section class="sala-section">
                    <h2 class="section-heading"><i class="bi bi-plus-circle"></i> ¿Listo para jugar?</h2>
                    <p class="section-help">Crea tu propio equipo o únete a uno ya creado.</p>
                    <button class="btn-primary btn-crear-equipo" onclick="abrirModalCrear()">
                        <i class="bi bi-shield-plus"></i> Crear equipo
                    </button>
                </section>
            `;
        }

        // EQUIPOS LIST SECTION
        html += `
            <section class="sala-section">
                <h2 class="section-heading">
                    <i class="bi bi-grid"></i> Equipos en esta sala
                    <span class="sala-count-badge">${data.equipos.length}</span>
                </h2>
                ${data.equipos.length === 0 ? `
                    <div class="equipos-empty">
                        <i class="bi bi-people"></i>
                        <p>Aún no hay equipos. ¡Sé el primero en crear uno!</p>
                    </div>
                ` : data.equipos.map(eq => {
                    const esMio = data.miEquipoId === eq.id;
                    const equipoLleno = eq.integrantes_count >= 8;
                    return `
                        <div class="equipo-card ${esMio ? 'equipo-card--mine' : ''}">
                            <div class="equipo-card-header">
                                <span class="equipo-nombre">${eq.nombre_equipo}</span>
                                <span class="equipo-count"><i class="bi bi-person"></i> ${eq.integrantes_count}/8</span>
                            </div>
                            <p class="equipo-lider"><i class="bi bi-star"></i> Líder: ${eq.lider_nombre}</p>
                            ${!data.miEquipoId ? `
                                <button class="btn-outline-small" onclick="unirseEquipo(${eq.id})" ${equipoLleno ? 'disabled' : ''}>
                                    <i class="bi ${equipoLleno ? 'bi-lock-fill' : 'bi-person-plus'}"></i>
                                    ${equipoLleno ? 'Grupo lleno' : 'Unirse'}
                                </button>
                            ` : esMio ? `
                                <span class="badge-mine"><i class="bi bi-check-circle-fill"></i> Tu equipo</span>
                            ` : ''}
                        </div>
                    `;
                }).join('')}
            </section>
        `;

        html += '</main>';
        container.innerHTML = html;

        // Re-adjuntar listener del botón de salir si es necesario
        const btnLeave = document.getElementById('btn-leave-room-dynamic');
        if (btnLeave) {
            btnLeave.onclick = (e) => {
                if (config.miEquipoId) {
                    e.preventDefault();
                    confirmarSalidaSala(btnLeave.href);
                }
            };
        }
    }

    async function confirmarSalidaSala(href) {
        const result = await Swal.fire({
            title: '¿Salir de la sala?',
            text: 'Actualmente estás en un equipo. Si sales de la sala, abandonarás también el equipo.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0ea5a4',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Salir y abandonar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            const ok = await salirEquipo(false);
            if (ok) window.location.href = href;
        }
    }

    function activarAutoPolling() {
        checkEstado();
        window.setInterval(checkEstado, 3000);
    }

    // ── Modal helpers ────────────────────────────────────────
    function abrirModalCrear() {
        const modal = document.getElementById('modalCrear');
        modal.hidden = false;
        document.getElementById('input-nombre-equipo').focus();
    }

    function cerrarModal(id) {
        document.getElementById(id).hidden = true;
    }

    // ── Crear equipo ─────────────────────────────────────────
    async function crearEquipo() {
        const input   = document.getElementById('input-nombre-equipo');
        const errorEl = document.getElementById('error-nombre-equipo');
        const nombre  = input.value.trim();

        if (!nombre || nombre.length < 3) {
            errorEl.textContent = 'El nombre debe tener al menos 3 caracteres.';
            input.classList.add('input-error');
            input.focus();
            return;
        }

        errorEl.textContent = '';
        input.classList.remove('input-error');

        const { status, data } = await postJson(config.crearUrl, { nombre_equipo: nombre });

        if (data.success || status < 300) {
            cerrarModal('modalCrear');
            mostrarNotificacion('¡Equipo creado!');
            checkEstado();
        } else {
            mostrarNotificacion(data.error ?? 'Error al crear el equipo.', true);
        }
    }

    // ── Unirse a equipo ──────────────────────────────────────
    async function unirseEquipo(equipoId) {
        const url = config.unirseUrlTemplate.replace('EQUIPO_ID', equipoId);
        const { status, data } = await postJson(url, {});

        if (data.success || status < 300) {
            mostrarNotificacion('¡Te uniste al equipo!');
            checkEstado();
        } else {
            mostrarNotificacion(data.error ?? 'No se pudo unir al equipo.', true);
        }
    }

    // ── Salir del equipo ─────────────────────────────────────
    async function salirEquipo(confirmar = true) {
        if (confirmar) {
            const result = await Swal.fire({
                title: '¿Abandonar equipo?',
                text: 'Si sales, perderás tu lugar en este equipo. Si eres el último integrante, el equipo se eliminará.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0ea5a4',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Cancelar'
            });

            if (!result.isConfirmed) return false;
        }

        const { status, data } = await postJson(config.salirUrl, {});

        if (data.success || status < 300) {
            mostrarNotificacion('Has salido del equipo.');
            checkEstado();
            return true;
        } else {
            mostrarNotificacion(data.error ?? 'No se pudo salir del equipo.', true);
            return false;
        }
    }

    // ── Backdrop del modal ──
    const modalCrear = document.getElementById('modalCrear');
    if (modalCrear) {
        modalCrear.addEventListener('click', function (e) {
            if (e.target === this) cerrarModal('modalCrear');
        });
    }

    // ── Expose to global scope ──────────────────────────────
    window.abrirModalCrear = abrirModalCrear;
    window.cerrarModal     = cerrarModal;
    window.crearEquipo     = crearEquipo;
    window.unirseEquipo    = unirseEquipo;
    window.salirEquipo     = salirEquipo;

    mostrarFlashServidorSiExiste();
    activarAutoPolling();

}());
