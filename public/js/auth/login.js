(function () {
    'use strict';

    var form = document.getElementById('login-form');

    function showError(fieldId, message) {
        var input = document.getElementById(fieldId);
        if (!input) return;
        input.classList.add('input-error');
        var group = input.closest('.form-group');
        if (group && !group.querySelector('.field-error')) {
            var span = document.createElement('span');
            span.className = 'field-error';
            span.textContent = message;
            group.appendChild(span);
        }
    }

    function clearError(fieldId) {
        var input = document.getElementById(fieldId);
        if (!input) return;
        input.classList.remove('input-error');
        var group = input.closest('.form-group');
        var existing = group && group.querySelector('.field-error');
        if (existing) existing.remove();
    }

    document.getElementById('email').onblur = function () {
        clearError('email');
        var v = this.value.trim();
        if (!v) {
            showError('email', 'El correo electrónico es obligatorio.');
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) {
            showError('email', 'Introduce un correo electrónico válido.');
        }
    };

    document.getElementById('password').onblur = function () {
        clearError('password');
        if (!this.value) {
            showError('password', 'La contraseña es obligatoria.');
        }
    };

    form.onsubmit = function (e) {
        var valid = true;
        clearError('email');
        clearError('password');

        var email = document.getElementById('email').value.trim();
        var pass  = document.getElementById('password').value;

        if (!email) {
            showError('email', 'El correo electrónico es obligatorio.');
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('email', 'Introduce un correo electrónico válido.');
            valid = false;
        }

        if (!pass) {
            showError('password', 'La contraseña es obligatoria.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    };
}());
