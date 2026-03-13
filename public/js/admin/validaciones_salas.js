/**
 * Validaciones en tiempo real para el formulario de Salas (Gimcanas)
 */

document.addEventListener('DOMContentLoaded', () => {
    initCustomSelects();
});

function initCustomSelects() {
    const containers = document.querySelectorAll('.custom-select-container');
    
    containers.forEach(container => {
        if (container.dataset.initialized) return;
        
        const searchInput = container.querySelector('.search-lugar-input');
        const hiddenInput = container.querySelector('.hidden-lugar-id');
        const dropdownList = container.querySelector('.custom-select-dropdown');
        const options = dropdownList.querySelectorAll('.custom-option');

        searchInput.addEventListener('focus', () => {
            dropdownList.style.display = 'block';
            searchInput.style.borderColor = '#0ea5a4';
            filterOptions(searchInput.value, options);
        });

        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) {
                dropdownList.style.display = 'none';
                searchInput.style.borderColor = hiddenInput.value ? '#0ea5a4' : '#cbd5e1';
                
                if (hiddenInput.value) {
                    const selectedOpt = Array.from(options).find(opt => opt.dataset.value === hiddenInput.value);
                    if (selectedOpt) searchInput.value = selectedOpt.textContent.trim();
                } else {
                    searchInput.value = '';
                }
                // trigger onblur validation for the lugar block
                validateLugarBlock(container);
            }
        });

        searchInput.addEventListener('input', (e) => {
            dropdownList.style.display = 'block';
            filterOptions(e.target.value, options);
            hiddenInput.value = '';
        });

        options.forEach(option => {
            option.addEventListener('click', () => {
                searchInput.value = option.textContent.trim();
                hiddenInput.value = option.dataset.value;
                dropdownList.style.display = 'none';
                
                options.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                
                // Clear error on the container when a lugar is selected
                searchInput.style.borderColor = '#0ea5a4';
                const errSpan = container.querySelector('.error-lugar-select');
                if (errSpan) errSpan.style.display = 'none';
            });
        });
        
        container.dataset.initialized = 'true';
    });
}

function filterOptions(searchTerm, optionsList) {
    const term = searchTerm.toLowerCase();
    optionsList.forEach(option => {
        const text = option.textContent.toLowerCase();
        option.style.display = text.includes(term) ? 'block' : 'none';
    });
}

// --- Single field validation ---

function validateField(inputId, type) {
    const input = document.getElementById(inputId);
    const errorSpan = document.getElementById('error-' + inputId);
    let isValid = true;
    let errorMessage = '';

    if (!input) return true;

    if (type === 'nombre') {
        const value = input.value.trim();
        if (value.length === 0) {
            isValid = false;
            errorMessage = 'El nombre es obligatorio.';
        } else if (value.length > 50) {
            isValid = false;
            errorMessage = 'El nombre no puede tener más de 50 caracteres.';
        }
    }

    if (type === 'descripcion') {
        const value = input.value.trim();
        if (value.length > 255) {
            isValid = false;
            errorMessage = 'La descripción no puede superar los 255 caracteres.';
        }
    }

    if (!isValid) {
        input.style.borderColor = '#ef4444';
        if (errorSpan) {
            errorSpan.textContent = errorMessage;
            errorSpan.style.display = 'block';
        }
    } else {
        input.style.borderColor = '#cbd5e1';
        if (errorSpan) errorSpan.style.display = 'none';
    }

    return isValid;
}

// --- Validate an individual reto block field (pregunta, respuesta, pista, lugar) ---

function validateLugarBlock(containerEl) {
    if (!containerEl) return;

    // Lugar select error
    const hiddenInput = containerEl.querySelector('.hidden-lugar-id');
    const searchInput = containerEl.querySelector('.search-lugar-input');
    let errLugar = containerEl.querySelector('.error-lugar-select');
    if (!errLugar) {
        errLugar = document.createElement('span');
        errLugar.className = 'error-message-text error-lugar-select';
        searchInput.parentNode.insertBefore(errLugar, searchInput.nextSibling);
    }
    if (!hiddenInput.value) {
        searchInput.style.borderColor = '#ef4444';
        errLugar.textContent = 'Debes seleccionar un lugar.';
        errLugar.style.display = 'block';
    } else {
        searchInput.style.borderColor = '#0ea5a4';
        errLugar.style.display = 'none';
    }
}

function validateTextareaField(textarea, minLength, errorMsg) {
    let errSpan = textarea.parentNode.querySelector('.error-inline-field');
    if (!errSpan) {
        errSpan = document.createElement('span');
        errSpan.className = 'error-message-text error-inline-field';
        textarea.parentNode.appendChild(errSpan);
    }
    const val = textarea.value.trim();
    if (val.length < minLength) {
        textarea.style.borderColor = '#ef4444';
        errSpan.textContent = errorMsg;
        errSpan.style.display = 'block';
        return false;
    } else {
        textarea.style.borderColor = '#cbd5e1';
        errSpan.style.display = 'none';
        return true;
    }
}

function validateInputField(input, minLength, errorMsg) {
    let errSpan = input.parentNode.querySelector('.error-inline-field');
    if (!errSpan) {
        errSpan = document.createElement('span');
        errSpan.className = 'error-message-text error-inline-field';
        input.parentNode.appendChild(errSpan);
    }
    const val = input.value.trim();
    if (val.length < minLength) {
        input.style.borderColor = '#ef4444';
        errSpan.textContent = errorMsg;
        errSpan.style.display = 'block';
        return false;
    } else {
        input.style.borderColor = '#cbd5e1';
        errSpan.style.display = 'none';
        return true;
    }
}

// --- Add blur handlers to all reto fields ---

function attachRetoBlurHandlers(containerSelector) {
    const container = document.querySelector(containerSelector);
    if (!container) return;

    container.querySelectorAll('.lugar-selector-container').forEach(block => {
        const [peguntaTa, pistaTa] = block.querySelectorAll('textarea');
        const respuestaInput = block.querySelector('input[type="text"]:not(.search-lugar-input)');
        const customSelectContainer = block.querySelector('.custom-select-container');

        if (customSelectContainer) {
            const searchInput = customSelectContainer.querySelector('.search-lugar-input');
            searchInput.addEventListener('blur', () => {
                setTimeout(() => validateLugarBlock(customSelectContainer), 200);
            });
        }

        if (peguntaTa) {
            peguntaTa.addEventListener('blur', () => validateTextareaField(peguntaTa, 10, 'La pregunta debe tener al menos 10 caracteres.'));
            peguntaTa.addEventListener('input', () => {
                if (peguntaTa.value.trim().length >= 10) {
                    peguntaTa.style.borderColor = '#cbd5e1';
                    const errSpan = peguntaTa.parentNode.querySelector('.error-inline-field');
                    if (errSpan) errSpan.style.display = 'none';
                }
            });
        }

        if (respuestaInput) {
            respuestaInput.addEventListener('blur', () => validateInputField(respuestaInput, 1, 'La respuesta correcta es obligatoria.'));
            respuestaInput.addEventListener('input', () => {
                if (respuestaInput.value.trim().length >= 1) {
                    respuestaInput.style.borderColor = '#cbd5e1';
                    const errSpan = respuestaInput.parentNode.querySelector('.error-inline-field');
                    if (errSpan) errSpan.style.display = 'none';
                }
            });
        }

        if (pistaTa) {
            pistaTa.addEventListener('blur', () => validateTextareaField(pistaTa, 5, 'La pista debe tener al menos 5 caracteres.'));
            pistaTa.addEventListener('input', () => {
                if (pistaTa.value.trim().length >= 5) {
                    pistaTa.style.borderColor = '#cbd5e1';
                    const errSpan = pistaTa.parentNode.querySelector('.error-inline-field');
                    if (errSpan) errSpan.style.display = 'none';
                }
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    attachRetoBlurHandlers('#add-lugares-container');
    // For edit pages, the container ID includes the sala ID
    document.querySelectorAll('[id^="edit-lugares-container-"]').forEach(el => {
        attachRetoBlurHandlers('#' + el.id);
    });
});

// --- Unique lugares check ---

function checkUniqueLugares(formPrefix, salaId = '') {
    const hiddenInputs = document.querySelectorAll(salaId 
        ? `#edit-lugares-container-${salaId} .hidden-lugar-id` 
        : `#add-lugares-container .hidden-lugar-id`);
    
    let selectedValues = [];
    let hasDuplicates = false;
    let anyEmpty = false;

    hiddenInputs.forEach((input, index) => {
        const blockId = salaId 
            ? `edit-lugar-block-${salaId}-${index}`
            : `add-lugar-block-${index}`;
        const block = document.getElementById(blockId);
        const val = input.value;
        
        if (!val) {
            anyEmpty = true;
            if (block) block.classList.add('error-border');
        } else if (selectedValues.includes(val)) {
            hasDuplicates = true;
            if (block) block.classList.add('error-border');
        } else {
            selectedValues.push(val);
        }
    });

    return { isValid: !hasDuplicates && !anyEmpty, hasDuplicates, anyEmpty };
}

// --- All reto fields check ---

function checkAllFieldsFilled(formPrefix, salaId = '') {
    const containerSelector = salaId ? `#edit-lugares-container-${salaId}` : '#add-lugares-container';
    const container = document.querySelector(containerSelector);
    if (!container) return false;

    const hiddenInputs = container.querySelectorAll('.hidden-lugar-id');

    let allValid = true;

    container.querySelectorAll('.lugar-selector-container').forEach(el => el.classList.remove('error-border'));

    hiddenInputs.forEach((input, index) => {
        const blockId = salaId ? `edit-lugar-block-${salaId}-${index}` : `add-lugar-block-${index}`;
        const block = document.getElementById(blockId);
        
        const blockTextareas = block.querySelectorAll('textarea');
        const pregunta = blockTextareas.length >= 1 ? blockTextareas[0].value.trim() : '';
        const pista    = blockTextareas.length >= 2 ? blockTextareas[1].value.trim() : '';
        
        const blockInputs = block.querySelectorAll('input[type="text"]:not(.search-lugar-input)');
        const respuesta = blockInputs.length >= 1 ? blockInputs[0].value.trim() : '';

        if (!input.value || pregunta.length < 10 || respuesta.length === 0 || pista.length < 5) {
            allValid = false;
            if (block) block.classList.add('error-border');
        }
    });

    return allValid;
}

// --- Full form validation (called before SweetAlert confirm) ---

function runFullValidation(formPrefix, salaId = '') {
    let isNombreValid = true;
    if (formPrefix === 'add') {
        isNombreValid = validateField('add-nombre', 'nombre');
        validateField('add-descripcion', 'descripcion');
    } else {
        isNombreValid = validateField(`edit-nombre-${salaId}`, 'nombre');
        validateField(`edit-descripcion-${salaId}`, 'descripcion');
    }

    const uniqueCheck = checkUniqueLugares(formPrefix, salaId || '');
    const fieldsCheck = checkAllFieldsFilled(formPrefix, salaId || '');

    const errorSpanId = salaId
        ? `error-edit-lugares-total-${salaId}`
        : 'error-add-lugares-total';
    const errorSpan = document.getElementById(errorSpanId);

    const isOverallValid = uniqueCheck.isValid && fieldsCheck;

    if (errorSpan) {
        if (!isOverallValid) {
            errorSpan.textContent = uniqueCheck.hasDuplicates
                ? 'No puedes seleccionar el mismo lugar más de una vez.'
                : 'Debes seleccionar exactamente 5 lugares distintos y rellenar completamente sus retos y pistas.';
            errorSpan.style.display = 'block';
        } else {
            errorSpan.style.display = 'none';
        }
    }

    return isNombreValid && isOverallValid;
}

// --- SweetAlert confirm wrappers ---

function validateFormAdd(event) {
    if (event) event.preventDefault();

    Swal.fire({
        title: '¿Guardar Gimcana?',
        text: 'Se creará la sala con los 5 retos configurados.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#0ea5a4',
        cancelButtonColor: '#64748b',
        customClass: {
            popup: 'premium-swal-popup',
            confirmButton: 'premium-swal-button',
            cancelButton: 'premium-swal-button',
        }
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('form-add-sala').submit();
        }
    });
}

function validateFormEdit(event, id) {
    if (event) event.preventDefault();

    Swal.fire({
        title: '¿Guardar Cambios?',
        text: 'Se actualizará la sala con los datos introducidos.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#0ea5a4',
        cancelButtonColor: '#64748b',
        customClass: {
            popup: 'premium-swal-popup',
            confirmButton: 'premium-swal-button',
            cancelButton: 'premium-swal-button',
        }
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('form-edit-sala').submit();
        }
    });
}

// --- Legacy alias for validateFormEditLocal (called inline on reto fields) ---
function validateFormEditLocal(id) {
    // Just a no-op live update, actual validation happens on submit
}
