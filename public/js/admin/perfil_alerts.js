/**
 * Profile Management Scripts
 * Handles avatar preview and field validations.
 */

function previewFile() {
    const preview = document.getElementById('previewImg');
    const file = document.getElementById('fotoInput').files[0];
    const reader = new FileReader();

    reader.onloadend = function () {
        preview.src = reader.result;
    }

    if (file) {
        reader.readAsDataURL(file);
    }
}

// Initialize validations when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const profileForm = document.querySelector('form[action*="perfil/update"]');
    if (profileForm && typeof attachValidations === 'function') {
        attachValidations(profileForm);
    }
});
