(function () {
    'use strict';

    const configNode = document.getElementById('grupos-config');
    if (!configNode) return;

    const config = {
        storeUrl: configNode.dataset.storeUrl,
        leaveUrl: configNode.dataset.leaveUrl,
        joinByCodeUrl: configNode.dataset.joinByCodeUrl,
        joinBaseUrl: configNode.dataset.joinBaseUrl,
        csrfToken: configNode.dataset.csrfToken,
        salaId: configNode.dataset.salaId && configNode.dataset.salaId !== 'null'
            ? JSON.parse(configNode.dataset.salaId)
            : null,
    };

    async function postJson(url, payload) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': config.csrfToken,
            },
            body: JSON.stringify(payload || {}),
        });

        let data = {};
        try {
            data = await res.json();
        } catch (_) {
            data = {};
        }

        return { ok: res.ok, status: res.status, data: data };
    }

    let toastTimer = null;
    function showToast(message, isError) {
        const toast = document.getElementById('toast');
        if (!toast) return;
        toast.textContent = message;
        toast.classList.toggle('error', !!isError);
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), 2600);
    }

    function abrirModalCrear() {
        const modal = document.getElementById('modalCrear');
        modal.hidden = false;
        const input = document.getElementById('input-nombre-grupo');
        if (input) input.focus();
    }

    function abrirModalCodigo() {
        const modal = document.getElementById('modalCodigo');
        if (!modal) return;
        modal.hidden = false;
        const input = document.getElementById('input-codigo-grupo');
        if (input) input.focus();
    }

    function cerrarModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.hidden = true;
    }

    async function crearGrupo() {
        const input = document.getElementById('input-nombre-grupo');
        const error = document.getElementById('error-nombre-grupo');
        const nombre = (input.value || '').trim();

        if (!nombre || nombre.length < 3) {
            error.textContent = 'El nombre debe tener al menos 3 caracteres.';
            input.classList.add('input-error');
            input.focus();
            return;
        }

        error.textContent = '';
        input.classList.remove('input-error');

        const { ok, data } = await postJson(config.storeUrl, { nombre_equipo: nombre });

        if (ok && data.success) {
            cerrarModal('modalCrear');
            showToast('Grupo creado correctamente');
            setTimeout(() => location.reload(), 500);
            return;
        }

        showToast(data.error || 'No se pudo crear el grupo', true);
    }

    async function unirseGrupo(equipoId) {
        const url = config.salaId
            ? `${config.joinBaseUrl}/${equipoId}/unirse`
            : `${config.joinBaseUrl}/${equipoId}/unirse`;
        const { ok, data } = await postJson(url, {});

        if (ok && data.success) {
            showToast('Te has unido al grupo');
            setTimeout(() => location.reload(), 500);
            return;
        }

        showToast(data.error || 'No se pudo unir al grupo', true);
    }

    async function salirGrupo() {
        const { ok, data } = await postJson(config.leaveUrl, { salaId: config.salaId });

        if (ok && data.success) {
            showToast('Has salido del grupo');
            setTimeout(() => location.reload(), 500);
            return;
        }

        showToast(data.error || 'No se pudo salir del grupo', true);
    }

    async function unirseConCodigo() {
        const input = document.getElementById('input-codigo-grupo');
        const error = document.getElementById('error-codigo-grupo');
        const codigo = (input.value || '').trim();

        if (!codigo || codigo.length < 6 || !/^\d+$/.test(codigo)) {
            error.textContent = 'Ingresa un código válido de 6 dígitos.';
            input.classList.add('input-error');
            input.focus();
            return;
        }

        error.textContent = '';
        input.classList.remove('input-error');

        const { ok, data } = await postJson(config.joinByCodeUrl, { codigo });

        if (ok && data.success) {
            cerrarModal('modalCodigo');
            showToast('Te has unido al grupo');
            setTimeout(() => location.reload(), 500);
            return;
        }

        showToast(data.error || 'No se pudo unir al grupo', true);
    }

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-action]');
        if (!trigger) return;

        const action = trigger.dataset.action;

        if (action === 'abrir-modal') {
            const modalId = trigger.dataset.modalId;
            if (modalId === 'modalCrear') abrirModalCrear();
            if (modalId === 'modalCodigo') abrirModalCodigo();
            return;
        }

        if (action === 'cerrar-modal') {
            const modalId = trigger.dataset.modalId;
            if (modalId) cerrarModal(modalId);
            return;
        }

        if (action === 'crear-grupo') {
            crearGrupo();
            return;
        }

        if (action === 'unirse-grupo') {
            const equipoId = Number(trigger.dataset.equipoId);
            if (Number.isFinite(equipoId) && equipoId > 0) {
                unirseGrupo(equipoId);
            }
            return;
        }

        if (action === 'unirse-codigo') {
            unirseConCodigo();
            return;
        }

        if (action === 'salir-grupo') {
            salirGrupo();
        }
    });

    const overlay = document.getElementById('modalCrear');
    if (overlay) {
        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) {
                cerrarModal('modalCrear');
            }
        });
    }

    const overlayCodigo = document.getElementById('modalCodigo');
    if (overlayCodigo) {
        overlayCodigo.addEventListener('click', function (event) {
            if (event.target === overlayCodigo) {
                cerrarModal('modalCodigo');
            }
        });
    }
})();
