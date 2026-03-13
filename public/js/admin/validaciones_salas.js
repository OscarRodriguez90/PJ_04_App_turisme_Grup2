/**
 * Validaciones en tiempo real para el formulario de Salas (Gimcanas)
 */

function validateField(inputId, type) {
    const input = document.getElementById(inputId);
    const errorSpan = document.getElementById('error-' + inputId);
    let isValid = true;
    let errorMessage = '';

    if (!input) return true;

    if (type === 'codigo') {
        const value = input.value.trim();
        if (value.length === 0) {
            isValid = false;
            errorMessage = 'El código es obligatorio.';
        } else if (value.length > 8) {
            isValid = false;
            errorMessage = 'El código no puede tener más de 8 caracteres.';
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
        if (errorSpan) {
            errorSpan.style.display = 'none';
        }
    }

    return isValid;
}

function checkUniqueLugares(formPrefix, salaId = '') {
    const selects = document.querySelectorAll(salaId 
        ? `#edit-lugares-container-${salaId} .lugar-select` 
        : `#add-lugares-container .lugar-select`);
    
    let selectedValues = [];
    let hasDuplicates = false;
    let anyEmpty = false;

    selects.forEach((select, index) => {
        const blockId = salaId 
            ? `edit-lugar-block-${salaId}-${index}`
            : `add-lugar-block-${index}`;
        const block = document.getElementById(blockId);
        const val = select.value;
        
        if (!val) {
            anyEmpty = true;
            block.classList.add('error-border');
        } else if (selectedValues.includes(val)) {
            hasDuplicates = true;
            block.classList.add('error-border');
        } else {
            selectedValues.push(val);
            // Optionally remove error border for specific block if other text fields are fine
        }
    });

    return { isValid: !hasDuplicates && !anyEmpty, hasDuplicates, anyEmpty };
}

function checkAllFieldsFilled(formPrefix, salaId = '') {
    const containerSelector = salaId ? `#edit-lugares-container-${salaId}` : '#add-lugares-container';
    const container = document.querySelector(containerSelector);
    if (!container) return false;

    const selects = container.querySelectorAll('select.lugar-select');
    const textareas = container.querySelectorAll('textarea');
    const inputs = container.querySelectorAll('input[type="text"]');

    let allValid = true;

    // Reset styles
    container.querySelectorAll('.lugar-selector-container').forEach(el => el.classList.remove('error-border'));

    selects.forEach((select, index) => {
        const blockId = salaId ? `edit-lugar-block-${salaId}-${index}` : `add-lugar-block-${index}`;
        const block = document.getElementById(blockId);
        const pregunta = textareas[index].value.trim();
        
        // Find inputs corresponding to this block (respuesta_correcta and pista)
        const blockInputs = block.querySelectorAll('input[type="text"]');
        let respuesta = '';
        let pista = '';
        if (blockInputs.length >= 2) {
            respuesta = blockInputs[0].value.trim();
            pista = blockInputs[1].value.trim();
        }

        if (!select.value || pregunta.length < 10 || respuesta.length === 0 || pista.length < 5) {
            allValid = false;
            block.classList.add('error-border');
        }
    });

    return allValid;
}

function validateFormAdd() {
    const isCodigoValid = validateField('add-codigo', 'codigo');
    
    // Lugares unique validation
    const uniqueCheck = checkUniqueLugares('add');
    
    // All info filled validation
    const fieldsCheck = checkAllFieldsFilled('add');
    
    const errorSpan = document.getElementById('error-add-lugares-total');
    let isOverallValid = uniqueCheck.isValid && fieldsCheck;

    if (!isOverallValid) {
        if (uniqueCheck.hasDuplicates) {
            errorSpan.textContent = 'No puedes seleccionar el mismo lugar más de una vez.';
        } else {
            errorSpan.textContent = 'Debes seleccionar exactamente 5 lugares distintos y rellenar completamente sus retos y pistas.';
        }
        errorSpan.style.display = 'block';
    } else {
        errorSpan.style.display = 'none';
    }

    return isCodigoValid && isOverallValid;
}

function validateFormEdit(id) {
    const isCodigoValid = validateField(`edit-codigo-${id}`, 'codigo');
    
    // Lugares unique validation
    const uniqueCheck = checkUniqueLugares('edit', id);
    
    // All info filled validation
    const fieldsCheck = checkAllFieldsFilled('edit', id);
    
    const errorSpan = document.getElementById(`error-edit-lugares-total-${id}`);
    let isOverallValid = uniqueCheck.isValid && fieldsCheck;

    if (!isOverallValid) {
        if (uniqueCheck.hasDuplicates) {
            errorSpan.textContent = 'No puedes seleccionar el mismo lugar más de una vez.';
        } else {
            errorSpan.textContent = 'Debes seleccionar exactamente 5 lugares distintos y rellenar completamente sus retos y pistas.';
        }
        errorSpan.style.display = 'block';
    } else {
        errorSpan.style.display = 'none';
    }

    return isCodigoValid && isOverallValid;
}
