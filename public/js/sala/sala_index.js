document.addEventListener('DOMContentLoaded', function() {
    const botonesEntrar = document.querySelectorAll('.btn-entrar-ajax');

    botonesEntrar.forEach(boton => {
        boton.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-hourglass-split"></i> Entrando...';

            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else if (data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al unirse',
                        text: data.error,
                        confirmButtonColor: '#0ea5a4'
                    });
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-door-open"></i> Entrar';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo contactar con el servidor.',
                    confirmButtonColor: '#0ea5a4'
                });
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-door-open"></i> Entrar';
            });
        });
    });
});
