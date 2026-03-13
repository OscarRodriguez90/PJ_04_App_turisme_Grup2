window.guardarUsuario = function(formData) {
    if (!formData) return;

    fetch(window.routes.apiStore, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(function(response) {
        if (!response.ok) {
            return response.json().then(function(err) { throw err; });
        }
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Añadido!',
                text: 'Usuario creado correctamente.',
                timer: 1500,
                showConfirmButton: false,
                customClass: { popup: 'premium-swal-popup' }
            });
            window.filtrarUsuarios();
        }
    })
    .catch(function(error) {
        console.error('Error guardar usuario:', error);
        let msg = 'Ocurrió un error al guardar el usuario.';
        if (error.errors) {
            let errorsArr = [];
            for (let prop in error.errors) {
                if(error.errors.hasOwnProperty(prop)) {
                    errorsArr.push(error.errors[prop][0]);
                }
            }
            msg = errorsArr.join('\n');
        } else if (error.message) {
            msg = error.message;
        }

        Swal.fire({
            icon: 'error',
            title: 'Error de validación',
            text: msg,
            customClass: {
                popup: 'premium-swal-popup',
                confirmButton: 'premium-swal-button btn-add-usuario'
            }
        });
    });
};

window.actualizarUsuario = function(id, formData) {
    if (!id || !formData) return;

    // Laravel uses POST with _method = PUT for FormData uploads
    fetch(window.routes.apiUpdate + '/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(function(response) {
        if (!response.ok) {
            return response.json().then(function(err) { throw err; });
        }
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: 'Usuario actualizado correctamente.',
                timer: 1500,
                showConfirmButton: false,
                customClass: { popup: 'premium-swal-popup' }
            });
            window.filtrarUsuarios();
        }
    })
    .catch(function(error) {
        console.error('Error actualizar usuario:', error);
        let msg = 'Ocurrió un error al actualizar el usuario.';
        if (error.errors) {
            let errorsArr = [];
            for (let prop in error.errors) {
                if(error.errors.hasOwnProperty(prop)) {
                    errorsArr.push(error.errors[prop][0]);
                }
            }
            msg = errorsArr.join('\n');
        }

        Swal.fire({
            icon: 'error',
            title: 'Error de validación',
            text: msg,
            customClass: {
                popup: 'premium-swal-popup',
                confirmButton: 'premium-swal-button btn-add-usuario'
            }
        });
    });
};

window.eliminarUsuario = function(id, nombre) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Deseas eliminar al usuario ' + nombre + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        customClass: { popup: 'premium-swal-popup' }
    }).then(function(result) {
        if (result.isConfirmed) {
            fetch(window.routes.apiDelete + '/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                if (!response.ok) throw new Error('Error al eliminar');
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: 'El usuario ha sido eliminado.',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'premium-swal-popup' }
                    });
                    window.filtrarUsuarios();
                }
            })
            .catch(function(error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al eliminar el usuario.',
                    customClass: { popup: 'premium-swal-popup' }
                });
            });
        }
    });
};
