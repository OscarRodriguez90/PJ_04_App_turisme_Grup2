/**
 * Inicialización y alertas (SweetAlert) para el CRUD de Salas (Gimcanas)
 */

function showAddModal() {
    const template = document.getElementById('modal-add-template');
    if (!template) {
        console.error('Template para añadir sala no encontrado');
        return;
    }

    Swal.fire({
        title: 'Nueva Gimcana',
        html: template.innerHTML,
        showCancelButton: true,
        confirmButtonText: 'Crear Gimcana',
        cancelButtonText: 'Cancelar',
        customClass: {
            container: 'swal2-container-custom',
            popup: 'swal2-popup-custom',
            header: 'swal2-header-custom',
            title: 'swal2-title-custom',
            content: 'swal2-content-custom-modal',
            actions: 'swal2-actions-custom',
            confirmButton: 'swal2-confirm-custom',
            cancelButton: 'swal2-cancel-custom'
        },
        buttonsStyling: false,
        width: '800px', // Wider modal to fit content
        didOpen: () => {
            // Re-assign listeners explicitly in the sweetalert DOM
            const popup = Swal.getPopup();
            
            const codigoInput = popup.querySelector('#add-codigo');
            if(codigoInput) {
                codigoInput.addEventListener('input', () => {
                    codigoInput.value = codigoInput.value.toUpperCase();
                    validateField('add-codigo', 'codigo');
                });
                codigoInput.addEventListener('blur', () => validateField('add-codigo', 'codigo'));
            }

            const selects = popup.querySelectorAll('.lugar-select');
            selects.forEach(select => {
                select.addEventListener('change', validateFormAdd);
            });

            const textareas = popup.querySelectorAll('textarea');
            textareas.forEach(ta => {
                ta.addEventListener('input', validateFormAdd);
            });

            const inputs = popup.querySelectorAll('input[type="text"]:not(#add-codigo)');
            inputs.forEach(inp => {
                inp.addEventListener('input', validateFormAdd);
            });
        },
        preConfirm: () => {
            if (!validateFormAdd()) {
                Swal.showValidationMessage('Por favor, corrige los errores antes de continuar.');
                return false;
            }
            
            // Si es válido, enviamos el formualrio que está DENTRO de SweetAlert
            document.querySelector('.swal2-container #form-add-sala').submit();
        }
    });
}

function handleEditClick(id) {
    const templateId = `modal-edit-template-${id}`;
    const template = document.getElementById(templateId);
    
    if (!template) {
        console.error(`Template para editar sala ${id} no encontrado`);
        return;
    }

    Swal.fire({
        title: 'Editar Gimcana',
        html: template.innerHTML,
        showCancelButton: true,
        confirmButtonText: 'Guardar Cambios',
        cancelButtonText: 'Cancelar',
        customClass: {
            container: 'swal2-container-custom',
            popup: 'swal2-popup-custom',
            header: 'swal2-header-custom',
            title: 'swal2-title-custom',
            content: 'swal2-content-custom-modal',
            actions: 'swal2-actions-custom',
            confirmButton: 'swal2-confirm-custom btn-edit-confirm',
            cancelButton: 'swal2-cancel-custom'
        },
        buttonsStyling: false,
        width: '800px',
        didOpen: () => {
            const popup = Swal.getPopup();
            
            const codigoInput = popup.querySelector(`#edit-codigo-${id}`);
            if(codigoInput) {
                codigoInput.addEventListener('input', () => {
                    codigoInput.value = codigoInput.value.toUpperCase();
                    validateField(`edit-codigo-${id}`, 'codigo');
                });
                codigoInput.addEventListener('blur', () => validateField(`edit-codigo-${id}`, 'codigo'));
            }

            const selects = popup.querySelectorAll('.lugar-select');
            selects.forEach(select => {
                select.addEventListener('change', () => validateFormEdit(id));
            });

            const textareas = popup.querySelectorAll('textarea');
            textareas.forEach(ta => {
                ta.addEventListener('input', () => validateFormEdit(id));
            });

            const inputs = popup.querySelectorAll(`input[type="text"]:not(#edit-codigo-${id})`);
            inputs.forEach(inp => {
                inp.addEventListener('input', () => validateFormEdit(id));
            });
        },
        preConfirm: () => {
            if (!validateFormEdit(id)) {
                Swal.showValidationMessage('Por favor, corrige los errores antes de guardar.');
                return false;
            }
            document.querySelector(`.swal2-container #form-edit-sala-${id}`).submit();
        }
    });
}

function handleDeleteClick(id, codigo) {
    Swal.fire({
        title: '¿Estás seguro?',
        html: `¿Estás seguro de que deseas eliminar la gimcana <strong>${codigo}</strong>?<br>Esta acción eliminará la sala y los 5 retos asociados.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        customClass: {
            confirmButton: 'swal2-confirm-custom btn-delete-confirm',
            cancelButton: 'swal2-cancel-custom'
        },
        buttonsStyling: false
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
