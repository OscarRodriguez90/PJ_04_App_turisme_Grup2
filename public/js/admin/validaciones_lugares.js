/**
 * Validaciones para el formulario de Lugares
 */

function validateField(input) {
    if (input.type === 'hidden') return true;

    const value = input.value.trim();
    const name = input.name;
    let isValid = true;
    let errorMessage = '';

    // Limpiar errores previos
    clearError(input);

    // 1. Validación de Obligatoriedad
    if (input.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Este campo es obligatorio';
    } 
    // 2. Validaciones Específicas por nombre de campo
    else if (value) {
        if (name === 'nombre') {
            if (value.length < 3) {
                isValid = false;
                errorMessage = 'El nombre es demasiado corto (mín. 3 caracteres)';
            } else if (value.length > 100) {
                isValid = false;
                errorMessage = 'El nombre es demasiado largo (máx. 100 caracteres)';
            }
        } 
        else if (name === 'descripcion') {
            if (value.length < 10) {
                isValid = false;
                errorMessage = 'La descripción debe tener al menos 10 caracteres';
            }
        }
        else if (name === 'direccion_completa') {
            if (value.length < 5) {
                isValid = false;
                errorMessage = 'Proporciona una dirección más completa';
            }
        }
        else if (name === 'latitud') {
            const lat = parseFloat(value);
            if (isNaN(lat)) {
                isValid = false;
                errorMessage = 'La latitud debe ser un número';
            } else if (lat < -90 || lat > 90) {
                isValid = false;
                errorMessage = 'La latitud debe estar entre -90 y 90';
            }
        } 
        else if (name === 'longitud') {
            const lon = parseFloat(value);
            if (isNaN(lon)) {
                isValid = false;
                errorMessage = 'La longitud debe ser un número';
            } else if (lon < -180 || lon > 180) {
                isValid = false;
                errorMessage = 'La longitud debe estar entre -180 y 180';
            }
        }
        else if (['username', 'apellido1', 'nombre'].includes(name) && input.closest('form[action*="perfil"]')) {
            if (value.length < 2) {
                isValid = false;
                errorMessage = 'Mínimo 2 caracteres';
            }
        }
        else if (name === 'password' && value.length > 0 && value.length < 6) {
            isValid = false;
            errorMessage = 'La contraseña debe tener al menos 6 caracteres';
        }
        else if (name === 'imagen' && input.files.length > 0) {
            const file = input.files[0];
            const sizeInMB = file.size / (1024 * 1024);
            if (sizeInMB > 10) {
                isValid = false;
                errorMessage = 'La imagen no puede superar los 10MB';
            }
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                isValid = false;
                errorMessage = 'Solo se permiten imágenes (JPG, PNG, GIF)';
            }
        }
    }

    if (!isValid) {
        showError(input, errorMessage);
    }
    return isValid;
}

function showError(input, message) {
    input.style.borderColor = '#ef4444';
    input.style.backgroundColor = '#fef2f2';
    
    let errorDisplay = input.parentNode.querySelector('.js-error-message');
    if (!errorDisplay) {
        errorDisplay = document.createElement('div');
        errorDisplay.className = 'js-error-message';
        errorDisplay.style.color = '#ef4444';
        errorDisplay.style.fontSize = '0.75rem';
        errorDisplay.style.marginTop = '0.25rem';
        errorDisplay.style.fontWeight = '500';
        input.parentNode.appendChild(errorDisplay);
    }
    errorDisplay.textContent = message;
}

function clearError(input) {
    input.style.borderColor = '';
    input.style.backgroundColor = '';
    const errorDisplay = input.parentNode.querySelector('.js-error-message');
    if (errorDisplay) {
        errorDisplay.remove();
    }
}

function attachValidations(popup) {
    const inputs = popup.querySelectorAll('input:not([type="hidden"]), select, textarea');
    inputs.forEach(input => {
        // Validar al salir del campo (onblur)
        input.addEventListener('blur', () => {
            validateField(input);
        });

        // Validar al escribir solo si ya hay un error
        input.addEventListener('input', () => {
            if (input.style.borderColor === 'rgb(239, 68, 68)') {
                validateField(input);
            }
        });
    });
}
