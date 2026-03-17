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
    async function salirEquipo() {
        const { status, data } = await postJson(config.salirUrl, {});

        if (data.success || status < 300) {
            mostrarNotificacion('Has salido del equipo.');
            reloadSoon();
        } else {
            mostrarNotificacion(data.error ?? 'No se pudo salir del equipo.', true);
        }
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

}());
