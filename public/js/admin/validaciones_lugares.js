/**
 * Validaciones para el formulario de Lugares
 */

function validateField(input) {
    const value = input.value.trim();
    const id = input.id;
    let isValid = true;
    let errorMessage = '';

    // Limpiar errores previos
    clearError(input);

    if (input.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Este campo es obligatorio';
    } else if (value) {
        if (id.includes('nombre') && (value.length < 3 || value.length > 255)) {
            isValid = false;
            errorMessage = 'El nombre debe tener entre 3 y 255 caracteres';
        } else if (id.includes('latitud')) {
            const lat = parseFloat(value);
            if (isNaN(lat) || lat < -90 || lat > 90) {
                isValid = false;
                errorMessage = 'La latitud debe estar entre -90 y 90';
            }
        } else if (id.includes('longitud')) {
            const lon = parseFloat(value);
            if (isNaN(lon) || lon < -180 || lon > 180) {
                isValid = false;
                errorMessage = 'La longitud debe estar entre -180 y 180';
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
    const inputs = popup.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => {
            if (input.style.borderColor === 'rgb(239, 68, 68)') { // Si ya tiene error, validar al escribir
                validateField(input);
            }
        });
    });
}
