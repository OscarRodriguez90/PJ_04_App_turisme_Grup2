/**
 * Global Admin Notifications
 * Handles success/error messages from session data.
 */

document.addEventListener('DOMContentLoaded', () => {
    const successMsg = document.getElementById('session-success-message')?.value;
    const errorMsg = document.getElementById('session-error-message')?.value;

    if (successMsg && typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: successMsg,
            confirmButtonColor: '#0ea5a4',
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'premium-swal-popup',
                confirmButton: 'premium-swal-button'
            }
        });
    }

    if (errorMsg && typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: '¡Ups!',
            text: errorMsg,
            confirmButtonColor: '#0ea5a4',
            customClass: {
                popup: 'premium-swal-popup',
                confirmButton: 'premium-swal-button'
            }
        });
    }
});
