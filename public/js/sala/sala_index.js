document.addEventListener('DOMContentLoaded', function() {
    const config = window.salaConfig || {};
    const botonesEntrar = document.querySelectorAll('.btn-entrar-ajax');

    botonesEntrar.forEach(boton => {
        boton.addEventListener('click', function(e) {
            const url = this.getAttribute('data-url');
            
            // Extract the Sala ID from the URL (e.g., /sala/4/entrar)
            const match = url.match(/\/sala\/(\d+)\/entrar/);
            const salaId = match ? parseInt(match[1]) : null;

            // Pure JS validation: if user is already in a different active gimcana
            if (config.gimcanaActivaId && config.gimcanaActivaId !== salaId) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Acción no permitida',
                    text: 'No puedes entrar porque ya perteneces a un grupo en otra gimcana.',
                    confirmButtonColor: '#0ea5a4'
                });
                return;
            }

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
