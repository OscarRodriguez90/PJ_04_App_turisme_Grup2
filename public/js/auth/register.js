(function () {
    'use strict';

    var form             = document.getElementById('register-form');
    var checkUsernameUrl = form.dataset.checkUsername;
    var checkEmailUrl    = form.dataset.checkEmail;
    var avatarInput      = document.getElementById('foto');
    var avatarBtn        = document.getElementById('avatar-upload-btn');
    var avatarPreview    = document.getElementById('avatar-preview');
    var avatarError      = document.getElementById('avatar-error');

    // ── Helpers ──────────────────────────────────────────────────────────────

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

    function xhrCheck(url, value, onExists) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url + '?value=' + encodeURIComponent(value), true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.exists) onExists();
                } catch (e) {}
            }
        };
        xhr.send();
    }

    function clearAvatarError() {
        if (avatarError) avatarError.textContent = '';
    }

    function showAvatarError(message) {
        if (avatarError) avatarError.textContent = message;
    }

    function resetAvatarPreview() {
        if (!avatarPreview) return;
        avatarPreview.classList.remove('has-image');
        avatarPreview.style.backgroundImage = '';
        avatarPreview.innerHTML = '' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
                '<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>' +
                '<circle cx="12" cy="7" r="4"/>' +
            '</svg>';
    }

    // ── Nombre ───────────────────────────────────────────────────────────────

    document.getElementById('nombre').onblur = function () {
        clearError('nombre');
        var v = this.value.trim();
        if (!v)         return showError('nombre', 'El nombre es obligatorio.');
        if (v.length < 3)  return showError('nombre', 'El nombre debe tener al menos 3 caracteres.');
        if (v.length > 25) return showError('nombre', 'El nombre no puede superar 25 caracteres.');
    };

    // ── Apellido ─────────────────────────────────────────────────────────────

    document.getElementById('apellido1').onblur = function () {
        clearError('apellido1');
        var v = this.value.trim();
        if (!v)         return showError('apellido1', 'El apellido es obligatorio.');
        if (v.length < 3)  return showError('apellido1', 'El apellido debe tener al menos 3 caracteres.');
        if (v.length > 50) return showError('apellido1', 'El apellido no puede superar 50 caracteres.');
    };

    // ── Username (incluye comprobación en BD) ─────────────────────────────────

    document.getElementById('username').onblur = function () {
        clearError('username');
        var v = this.value.trim();
        if (!v)         return showError('username', 'El nombre de usuario es obligatorio.');
        if (v.length < 3)  return showError('username', 'El usuario debe tener al menos 3 caracteres.');
        if (v.length > 25) return showError('username', 'El usuario no puede superar 25 caracteres.');
        if (!/^[a-zA-Z0-9_]+$/.test(v)) return showError('username', 'Solo letras, números y guión bajo.');
        xhrCheck(checkUsernameUrl, v, function () {
            showError('username', 'Este nombre de usuario ya está en uso.');
        });
    };

    // ── Email (incluye comprobación en BD) ───────────────────────────────────

    document.getElementById('email').onblur = function () {
        clearError('email');
        var v = this.value.trim();
        if (!v) return showError('email', 'El correo electrónico es obligatorio.');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) return showError('email', 'Introduce un correo electrónico válido.');
        xhrCheck(checkEmailUrl, v, function () {
            showError('email', 'Este correo ya tiene una cuenta registrada.');
        });
    };

    // ── Contraseña ───────────────────────────────────────────────────────────

    document.getElementById('password').onblur = function () {
        clearError('password');
        var v = this.value;
        if (!v)        return showError('password', 'La contraseña es obligatoria.');
        if (v.length < 8) return showError('password', 'La contraseña debe tener al menos 8 caracteres.');
    };

    // ── Confirmar contraseña ─────────────────────────────────────────────────

    document.getElementById('password_confirmation').onblur = function () {
        clearError('password_confirmation');
        var v    = this.value;
        var pass = document.getElementById('password').value;
        if (!v)      return showError('password_confirmation', 'Confirma tu contraseña.');
        if (v !== pass) return showError('password_confirmation', 'Las contraseñas no coinciden.');
    };

    // ── Avatar (subida real + preview) ─────────────────────────────────────

    if (avatarBtn && avatarInput) {
        avatarBtn.onclick = function () {
            avatarInput.click();
        };
    }

    if (avatarInput) {
        avatarInput.onchange = function () {
            clearAvatarError();

            if (!avatarInput.files || !avatarInput.files.length) {
                resetAvatarPreview();
                return;
            }

            var file = avatarInput.files[0];
            var allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            var maxSizeBytes = 2 * 1024 * 1024;

            if (allowedTypes.indexOf(file.type) === -1) {
                avatarInput.value = '';
                resetAvatarPreview();
                showAvatarError('La imagen debe ser JPG, PNG o WEBP.');
                return;
            }

            if (file.size > maxSizeBytes) {
                avatarInput.value = '';
                resetAvatarPreview();
                showAvatarError('La imagen no puede superar 2MB.');
                return;
            }

            var reader = new FileReader();
            reader.onload = function (e) {
                if (!avatarPreview) return;
                avatarPreview.classList.add('has-image');
                avatarPreview.style.backgroundImage = 'url("' + e.target.result + '")';
                avatarPreview.innerHTML = '';
            };
            reader.readAsDataURL(file);
        };
    }

    // ── Guard de envío ───────────────────────────────────────────────────────

    form.onsubmit = function (e) {
        var required  = ['nombre', 'apellido1', 'username', 'email', 'password', 'password_confirmation'];
        var hasErrors = form.querySelectorAll('.field-error').length > 0;

        required.forEach(function (id) {
            var input = document.getElementById(id);
            if (input && !input.value.trim()) {
                if (!input.closest('.form-group').querySelector('.field-error')) {
                    showError(id, 'Este campo es obligatorio.');
                }
                hasErrors = true;
            }
        });

        if (hasErrors) e.preventDefault();
    };

    resetAvatarPreview();
}());
