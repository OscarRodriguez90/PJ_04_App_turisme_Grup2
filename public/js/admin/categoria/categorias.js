(function () {
    'use strict';

    var baseUrl = document.getElementById('categorias-app').dataset.baseUrl;

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
