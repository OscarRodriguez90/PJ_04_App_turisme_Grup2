(function () {
    'use strict';

    var baseUrl = document.getElementById('categorias-app').dataset.baseUrl;
    var form = document.getElementById('form-categoria');

    function getGroup(input) {
        return input.closest('.modal-form-group');
    }

    function showError(fieldId, message) {
        var input = document.getElementById(fieldId);
        if (!input) return;

        input.classList.add('input-error');
        var group = getGroup(input);
        if (!group) return;

        var existing = group.querySelector('.field-error');
        if (existing) {
            existing.textContent = message;
            return;
        }

        var span = document.createElement('span');
        span.className = 'field-error';
        span.textContent = message;
        group.appendChild(span);
    }

    function clearError(fieldId) {
        var input = document.getElementById(fieldId);
        if (!input) return;

        input.classList.remove('input-error');
        var group = getGroup(input);
        var existing = group && group.querySelector('.field-error');
        if (existing) existing.remove();
    }

    function clearAllErrors() {
        clearError('input-nombre');
        clearError('input-icono');
        clearError('input-color-text');
    }

    function validateNombre() {
        var input = document.getElementById('input-nombre');
        if (!input) return true;

        clearError('input-nombre');
        var value = input.value.trim();

        if (!value) {
            showError('input-nombre', 'El nombre es obligatorio.');
            return false;
        }

        if (value.length > 100) {
            showError('input-nombre', 'El nombre no puede superar 100 caracteres.');
            return false;
        }

        return true;
    }

    function validateIcono() {
        var input = document.getElementById('input-icono');
        if (!input) return true;

        clearError('input-icono');
        var value = input.value.trim();

        if (!value) return true;

        if (value.length > 100) {
            showError('input-icono', 'El icono no puede superar 100 caracteres.');
            return false;
        }

        if (!/^[a-zA-Z0-9_\- ]+$/.test(value)) {
            showError('input-icono', 'El icono contiene caracteres no permitidos.');
            return false;
        }

        return true;
    }

    function validateColor() {
        var input = document.getElementById('input-color-text');
        if (!input) return true;

        clearError('input-color-text');
        var value = input.value.trim();

        if (!value) {
            input.value = '#0ea5a4';
            syncPicker(input.value);
            return true;
        }

        if (!/^#[0-9a-fA-F]{6}$/.test(value)) {
            showError('input-color-text', 'El color debe tener formato hexadecimal (#RRGGBB).');
            return false;
        }

        return true;
    }

    function previewIcon(val) {
        var box = document.getElementById('icono-preview');
        if (box) box.innerHTML = val ? '<i class="' + val + '"></i>' : '';
    }

    function openCreate() {
        document.getElementById('modal-form-title').textContent = 'Nueva Categoría';
        document.getElementById('form-categoria').action = baseUrl;
        document.getElementById('form-method-field').innerHTML = '';
        document.getElementById('input-nombre').value = '';
        document.getElementById('input-icono').value = '';
        previewIcon('');
        document.getElementById('input-color-picker').value = '#0ea5a4';
        document.getElementById('input-color-text').value = '#0ea5a4';
        clearAllErrors();
        document.getElementById('modal-form').classList.remove('hidden');
    }

    function openEdit(id, nombre, icono, color) {
        document.getElementById('modal-form-title').textContent = 'Editar Categoría';
        document.getElementById('form-categoria').action = baseUrl + '/' + id;
        document.getElementById('form-method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('input-nombre').value = nombre;
        document.getElementById('input-icono').value = icono;
        previewIcon(icono);
        document.getElementById('input-color-picker').value = color;
        document.getElementById('input-color-text').value = color;
        clearAllErrors();
        document.getElementById('modal-form').classList.remove('hidden');
    }

    function openDelete(id, nombre) {
        document.getElementById('delete-nombre').textContent = nombre;
        document.getElementById('form-delete').action = baseUrl + '/' + id;
        document.getElementById('modal-delete').classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function syncColor(val) {
        document.getElementById('input-color-text').value = val;
    }

    function syncPicker(val) {
        if (/^#[0-9a-fA-F]{6}$/.test(val)) {
            document.getElementById('input-color-picker').value = val;
        }
    }

    document.getElementById('input-nombre').addEventListener('blur', validateNombre);
    document.getElementById('input-icono').addEventListener('blur', function () {
        if (validateIcono()) previewIcon(this.value.trim());
    });
    document.getElementById('input-color-text').addEventListener('blur', validateColor);

    form.addEventListener('submit', function (e) {
        clearAllErrors();

        var okNombre = validateNombre();
        var okIcono = validateIcono();
        var okColor = validateColor();

        if (!okNombre || !okIcono || !okColor) {
            e.preventDefault();
        }
    });

    // Expose to inline onclick handlers
    window.openCreate  = openCreate;
    window.openEdit    = openEdit;
    window.openDelete  = openDelete;
    window.closeModal  = closeModal;
    window.syncColor   = syncColor;
    window.syncPicker  = syncPicker;
    window.previewIcon = previewIcon;

    // Close on backdrop click
    document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) overlay.classList.add('hidden');
        });
    });
}());
