/**
 * Places Module Initialization
 * Handles automatic modal re-opening after server-side validation errors.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Check if there's a specific modal to re-open (signaled by Blade)
    const reOpenEditId = document.getElementById('reopen-edit-id')?.value;
    const reOpenAdd = document.getElementById('reopen-add-flag')?.value;
    const hasAddParam = new URLSearchParams(window.location.search).has('action') && new URLSearchParams(window.location.search).get('action') === 'add';

    if (reOpenEditId) {
        if (typeof handleEditClick === 'function') {
            handleEditClick(reOpenEditId);
        }
    } else if (reOpenAdd === '1' || hasAddParam) {
        if (typeof showAddModal === 'function') {
            showAddModal();
        }
    }
});
