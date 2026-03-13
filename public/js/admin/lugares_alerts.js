/**
 * Places Management Alerts
 * Handles SweetAlert2 modals for place details, creation, and editing.
 */

// Click Handlers
function handleCardClick(id, event) {
    if (event) event.stopPropagation();
    const template = document.getElementById(`modal-view-template-${id}`);
    if (!template) return;

    // Get the title from the card (simpler than putting it in template)
    const card = document.querySelector(`.lugar-card[data-id="${id}"]`);
    const nombre = card ? card.querySelector('.lugar-title').innerText : 'Detalles del Lugar';

    Swal.fire({
        title: `<div style="color: #0f172a; font-weight: 700; font-family: 'Inter', sans-serif;">${nombre}</div>`,
        html: template.innerHTML,
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#0ea5a4',
        padding: '2rem',
        borderRadius: '1rem',
        showCloseButton: true,
        customClass: {
            popup: 'premium-swal-popup',
            confirmButton: 'premium-swal-button'
        }
    });
}

function handleEditClick(id, event) {
    if (event) event.stopPropagation();
    showEditModal(id);
}

function handleDeleteClick(id, nombre, event) {
    if (event) event.stopPropagation();
    confirmDelete(id, nombre);
}

// Remove showPlaceDetails as it's merged into handleCardClick or not needed

function showAddModal() {
    const template = document.getElementById('modal-add-template');
    if (!template) return;

    const formHtml = template.innerHTML;

    Swal.fire({
        title: '<div style="font-family: \'Inter\', sans-serif; font-weight: 700;">Añadir Nuevo Lugar</div>',
        html: formHtml,
        didOpen: (popup) => {
            if (typeof attachValidations === 'function') {
                attachValidations(popup);
            }
        },
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Crear Lugar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#0ea5a4',
        cancelButtonColor: '#64748b',
        preConfirm: () => {
            const form = Swal.getPopup().querySelector('#addForm');
            const inputs = form.querySelectorAll('input:not([type="hidden"]), select, textarea');
            let allValid = true;

            inputs.forEach(input => {
                if (!validateField(input)) {
                    allValid = false;
                }
            });

            if (!allValid || !form.checkValidity()) {
                Swal.showValidationMessage('Por favor, corrige los errores en el formulario');
                return false;
            }
            return form;
        },
        customClass: {
            popup: 'premium-swal-popup',
            confirmButton: 'premium-swal-button',
            cancelButton: 'premium-swal-button'
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const form = result.value;
            const nombre = form.querySelector('#swal-add-nombre').value;
            
            Swal.fire({
                title: '¿Confirmar creación?',
                text: `¿Estás seguro de que quieres crear el lugar "${nombre}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0ea5a4',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, crear',
                cancelButtonText: 'Revisar',
                customClass: {
                    popup: 'premium-swal-popup',
                    confirmButton: 'premium-swal-button',
                    cancelButton: 'premium-swal-button'
                }
            }).then((confirmResult) => {
                if (confirmResult.isConfirmed) {
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    });
}

function showEditModal(id) {
    const template = document.getElementById(`modal-edit-template-${id}`);
    if (!template) return;

    Swal.fire({
        title: '<div style="font-family: \'Inter\', sans-serif; font-weight: 700;">Editar Lugar</div>',
        html: template.innerHTML,
        didOpen: (popup) => {
            if (typeof attachValidations === 'function') {
                attachValidations(popup);
            }
        },
        showCancelButton: true,
        confirmButtonText: 'Guardar Cambios',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#0ea5a4',
        cancelButtonColor: '#64748b',
        preConfirm: () => {
            const form = Swal.getPopup().querySelector('form');
            const inputs = form.querySelectorAll('input:not([type="hidden"]), select, textarea');
            let allValid = true;

            inputs.forEach(input => {
                if (!validateField(input)) {
                    allValid = false;
                }
            });

            if (!allValid || !form.checkValidity()) {
                Swal.showValidationMessage('Por favor, corrige los errores en el formulario');
                return false;
            }
            return form;
        },
        customClass: {
            popup: 'premium-swal-popup',
            confirmButton: 'premium-swal-button',
            cancelButton: 'premium-swal-button'
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const form = result.value;
            const nombreInput = form.querySelector('[name="nombre"]');
            const nombre = nombreInput ? nombreInput.value : 'este lugar';

            Swal.fire({
                title: '¿Guardar cambios?',
                text: `¿Estás seguro de que quieres actualizar el lugar "${nombre}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0ea5a4',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Revisar',
                customClass: {
                    popup: 'premium-swal-popup',
                    confirmButton: 'premium-swal-button',
                    cancelButton: 'premium-swal-button'
                }
            }).then((confirmResult) => {
                if (confirmResult.isConfirmed) {
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    });
}

// Remove submitFormWithData as it's no longer used

function confirmDelete(id, nombre) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Vas a eliminar "${nombre}". Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        customClass: {
            popup: 'premium-swal-popup',
            confirmButton: 'premium-swal-button',
            cancelButton: 'premium-swal-button'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/lugares/${id}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
