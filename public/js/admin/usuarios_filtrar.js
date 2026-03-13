window.filtrarUsuarios = function() {
    const texto = document.getElementById('filtroTexto').value;
    const rol = document.getElementById('filtroRol').value;

    let url = window.routes.apiUsuarios + '?';
    if(texto) url += 'nombre=' + encodeURIComponent(texto) + '&';
    if(rol)   url += 'rol=' + encodeURIComponent(rol);

    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('Error al cargar datos');
        }
        return response.json();
    })
    .then(function(data) {
        window.renderUsuarios(data);
    })
    .catch(function(error) {
        console.error('Error fetching usuarios:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar los usuarios.',
            customClass: {
                popup: 'premium-swal-popup',
                confirmButton: 'premium-swal-button btn-add-usuario'
            }
        });
    });
};
