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
            return;
        }

        if (window.salaFlash.error) {
            mostrarNotificacion(window.salaFlash.error, true);
        }
    }

    function activarAutoEntradaGimcana() {
        if (!config || !config.miEquipoId || !config.estadoLiveUrl || !config.mapaUrl) {
            return;
        }

        if (config.salaEstado === 'jugando') {
            window.location.href = config.mapaUrl;
            return;
        }

        const checkEstado = async () => {
            try {
                const res = await fetch(config.estadoLiveUrl, {
                    headers: {
                        Accept: 'application/json',
                    },
                });

                if (!res.ok) {
                    return;
                }

                const payload = await res.json();
                if (payload && payload.estado === 'jugando' && payload.miEquipoId) {
                    window.location.href = config.mapaUrl;
                }
            } catch (_) {
                // Keep polling in next cycle.
            }
        };

        checkEstado();
        window.setInterval(checkEstado, 5000);
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
            reloadSoon();
        } else {
            mostrarNotificacion(data.error ?? 'Error al crear el equipo.', true);
        }
    }

    // ── Unirse a equipo ──────────────────────────────────────
    async function unirseEquipo(equipoId) {
        const url = `/sala/${config.salaId}/equipos/${equipoId}/unirse`;
        const { status, data } = await postJson(url, {});

        if (data.success || status < 300) {
            mostrarNotificacion('¡Te uniste al equipo!');
            reloadSoon();
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
            if (!confirmar) return true; // Para uso en el botón de la cabecera
            reloadSoon();
        } else {
            mostrarNotificacion(data.error ?? 'No se pudo salir del equipo.', true);
            return false;
        }
    }

    // Interceptar botón de salir de la sala en la cabecera
    const btnLeaveRoom = document.getElementById('btn-leave-room');
    if (btnLeaveRoom) {
        btnLeaveRoom.addEventListener('click', async function (e) {
            if (config.miEquipoId) {
                e.preventDefault();
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
                    if (ok) {
                        window.location.href = btnLeaveRoom.href;
                    }
                }
            }
        });
    }

    // ── Close modal on backdrop click ────────────────────────
    document.getElementById('modalCrear').addEventListener('click', function (e) {
        if (e.target === this) cerrarModal('modalCrear');
    });

    // ── Expose to global scope (used in onclick= attributes) ─
    window.abrirModalCrear = abrirModalCrear;
    window.cerrarModal     = cerrarModal;
    window.crearEquipo     = crearEquipo;
    window.unirseEquipo    = unirseEquipo;
    window.salirEquipo     = salirEquipo;

    mostrarFlashServidorSiExiste();
    activarAutoEntradaGimcana();

}());
