/**
 * Inicializador post-carga para auto-reabrir modales de Salas en caso de error PHP
 */
document.addEventListener('DOMContentLoaded', () => {
    // Reopen edit modal if PHP validation failed for an existing ID
    const editIdInput = document.getElementById('reopen-edit-id');
    if (editIdInput && editIdInput.value) {
        handleEditClick(editIdInput.value);
    } 
    // Otherwise open add modal if the add-form failed validation
    else {
        const addFlag = document.getElementById('reopen-add-flag');
        if (addFlag && addFlag.value === "1") {
            showAddModal();
        }
    }
});
