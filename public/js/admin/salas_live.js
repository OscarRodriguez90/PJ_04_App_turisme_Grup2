(function () {
    'use strict';

    const config = window.adminSalasConfig || {};

    function getCsrfToken() {
        const tokenNode = document.querySelector('meta[name="csrf-token"]');
        return tokenNode ? tokenNode.content : '';
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    function renderGruposLive(salas) {
        if (!Array.isArray(salas)) return;

        salas.forEach((sala) => {
            const block = document.querySelector('.grupos-live-block[data-sala-id="' + sala.id + '"]');
            if (!block) return;

            const list = block.querySelector('.js-grupos-live-list');
            if (!list) return;

            const grupos = Array.isArray(sala.grupos) ? sala.grupos : [];

            if (grupos.length === 0) {
                list.innerHTML = '<li class="grupos-empty">Sin grupos en esta sala</li>';
                return;
            }

            list.innerHTML = grupos.map((grupo) => {
                const nombre = escapeHtml(grupo.nombre || 'Grupo');
                const count = Number.isFinite(grupo.integrantes_count) ? grupo.integrantes_count : 0;
                const etiqueta = count === 1 ? 'jugador' : 'jugadores';

                return '<li><span>' + nombre + '</span><span class="grupo-count">' + count + ' ' + etiqueta + '</span></li>';
            }).join('');
        });
    }

    function refreshGruposLive() {
        if (!config.liveGruposUrl) return;

        fetch(config.liveGruposUrl, {
            headers: {
                Accept: 'application/json',
            },
        })
            .then((res) => {
                if (!res.ok) throw new Error('No se pudo cargar grupos en vivo');
                return res.json();
            })
            .then((payload) => {
                renderGruposLive(payload.salas);
            })
            .catch(() => {
                // Silent fail: next polling cycle retries automatically.
            });
    }

    function updateEstado(salaId, estado) {
        const csrfToken = getCsrfToken();
        const baseUrl = config.updateEstadoBaseUrl || '/admin/salas';

        return fetch(baseUrl + '/' + salaId + '/estado', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            body: JSON.stringify({ estado: estado }),
        }).then(async (res) => {
            let payload = {};

            try {
                payload = await res.json();
            } catch (_) {
                payload = {};
            }

            if (!res.ok) {
                throw new Error(payload.error || 'Error del servidor');
            }

            return payload;
        });
    }

    function empezarGimcana(buttonEl, salaId) {
        buttonEl.disabled = true;

        updateEstado(salaId, 'jugando')
            .then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Gimcana iniciada',
                    text: 'El estado ha cambiado a "jugando".',
                    timer: 1200,
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 900);
            })
            .catch((error) => {
                buttonEl.disabled = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'No se pudo iniciar la gimcana.',
                });
            });
    }

    function reiniciarPartida(buttonEl, salaId) {
        buttonEl.disabled = true;

        updateEstado(salaId, 'esperando')
            .then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Partida reiniciada',
                    text: 'El estado ha cambiado a "esperando".',
                    timer: 1200,
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 900);
            })
            .catch(() => {
                buttonEl.disabled = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo reiniciar la partida.',
                });
            });
    }

    refreshGruposLive();
    window.setInterval(refreshGruposLive, 7000);

    // Expose for inline onclick handlers in blade cards.
    window.empezarGimcana = empezarGimcana;
    window.reiniciarPartida = reiniciarPartida;
}());
