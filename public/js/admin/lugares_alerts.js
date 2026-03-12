/**
 * Places Management Alerts
 * Handles SweetAlert2 modals for place details, creation, and editing.
 */

// Helper to extract data from a card element
function getPlaceData(element) {
    const card = element.closest('.lugar-card');
    return {
        id: card.dataset.id,
        nombre: card.dataset.nombre,
        descripcion: card.dataset.descripcion,
        direccion: card.dataset.direccion,
        latitud: card.dataset.latitud,
        longitud: card.dataset.longitud,
        id_categoria: card.dataset.idCategoria,
        categoria: card.dataset.categoriaNombre,
        color: card.dataset.color,
        imagen: card.dataset.imagen
    };
}

// Click Handlers
function handleCardClick(element) {
    showPlaceDetails(getPlaceData(element));
}

function handleEditClick(element, event) {
    event.stopPropagation();
    showEditModal(getPlaceData(element));
}

function handleDeleteClick(element, event) {
    event.stopPropagation();
    const data = getPlaceData(element);
    confirmDelete(data.id, data.nombre);
}

// Modal Functions
function showPlaceDetails(data) {
    Swal.fire({
        title: `<div style="color: #0f172a; font-weight: 700; font-family: 'Inter', sans-serif;">${data.nombre}</div>`,
        html: `
            <div style="text-align: left; font-family: 'Inter', sans-serif; color: #475569; line-height: 1.6;">
                <div style="margin-bottom: 1rem; padding: 1rem; background: #f8fafc; border-radius: 0.75rem; border-left: 4px solid ${data.color}">
                    <strong style="color: #1e293b; display: block; margin-bottom: 0.25rem;">Descripción</strong>
                    <p style="margin: 0;">${data.descripcion || 'Sin descripción disponible.'}</p>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div style="background: #f1f5f9; padding: 0.75rem; border-radius: 0.5rem;">
                        <strong style="color: #475569; font-size: 0.8rem; text-transform: uppercase;">Dirección</strong>
                        <p style="margin: 0.25rem 0 0 0; font-weight: 500;">${data.direccion || 'N/A'}</p>
                    </div>
                    <div style="background: #f1f5f9; padding: 0.75rem; border-radius: 0.5rem;">
                        <strong style="color: #475569; font-size: 0.8rem; text-transform: uppercase;">Categoría</strong>
                        <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: ${data.color};">${data.categoria}</p>
                    </div>
                </div>

                <div style="margin-top: 1rem; background: #f1f5f9; padding: 0.75rem; border-radius: 0.5rem;">
                    <strong style="color: #475569; font-size: 0.8rem; text-transform: uppercase;">Coordenadas</strong>
                    <p style="margin: 0.25rem 0 0 0; font-family: monospace;">Lat: ${data.latitud}, Long: ${data.longitud}</p>
                </div>
            </div>
        `,
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
            if (!form.checkValidity()) {
                Swal.showValidationMessage('Por favor, rellena todos los campos obligatorios');
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
                    form.submit();
                }
            });
        }
    });
}

function showEditModal(data) {
    const template = document.getElementById('modal-edit-template');
    if (!template) return;

    Swal.fire({
        title: '<div style="font-family: \'Inter\', sans-serif; font-weight: 700;">Editar Lugar</div>',
        html: template.innerHTML,
        didOpen: (popup) => {
            const setVal = (selector, val) => {
                const el = popup.querySelector(selector);
                if (el && !el.value) el.value = val;
            };

            setVal('#swal-input-nombre', data.nombre);
            setVal('#swal-input-descripcion', data.descripcion || '');
            setVal('#swal-input-direccion', data.direccion);
            setVal('#swal-input-latitud', data.latitud);
            setVal('#swal-input-longitud', data.longitud);
            setVal('#swal-input-categoria', data.id_categoria);
            setVal('#swal-edit-id', data.id);

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
            const form = Swal.getPopup().querySelector('#editForm');
            if (!form.checkValidity()) {
                Swal.showValidationMessage('Por favor, rellena todos los campos obligatorios');
                return false;
            }
            form.action = `/admin/lugares/${data.id}`;
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
            const nombre = form.querySelector('#swal-input-nombre').value;

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
