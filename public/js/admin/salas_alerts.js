/**
 * Alertas (SweetAlert) para el CRUD de Salas (Gimcanas)
 * Maneja principalmente confirmaciones de borrado ya que la creación y edición 
 * ahora suceden en páginas dedicadas.
 */

function handleDeleteClick(id, codigo) {
    Swal.fire({
        title: '¿Estás seguro?',
        html: `¿Estás seguro de que deseas eliminar la gimcana <strong>${codigo}</strong>?<br>Esta acción eliminará la sala y los 5 retos asociados.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'premium-swal-popup',
            confirmButton: 'premium-swal-button',
            cancelButton: 'premium-swal-button'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Creamos un formulario dinámico para enviar la petición DELETE
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/salas/${id}`;
            form.style.display = 'none';

            // Token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken;
            form.appendChild(tokenInput);

            // Método DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

