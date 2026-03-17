(function () {
    'use strict';
        const locateBtn = document.getElementById('locateBtn');
        const userLocDisplay = document.getElementById('userLocDisplay');
        const distanceDisplay = document.getElementById('distanceDisplay');
        const locationMessage = document.getElementById('locationMessage');

        const config = locateBtn
            ? {
                lat: Number.parseFloat(locateBtn.dataset.lat || '0'),
                lng: Number.parseFloat(locateBtn.dataset.lng || '0'),
                nombre: locateBtn.dataset.nombre || 'Destino',
            }
            : null;

        if (!config || Number.isNaN(config.lat) || Number.isNaN(config.lng)) {
            return;
        }

        // ── Estado ──
        const state = {
            userLocation: null,
        };

        /**
         * Calcula distancia en metros entre dos puntos (Fórmula de Haversine)
         */
        function getDistanceMeters(lat1, lon1, lat2, lon2) {
            const toRad = (degrees) => (degrees * Math.PI) / 180;
            const R = 6371000; // Radio de la Tierra en metros
            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return Math.round(R * c);
        }

        /**
         * Formatea distancia para mostrar en la UI
         */
        function formatDistance(meters) {
            if (meters < 1000) {
                return meters + ' m';
            }
            return (meters / 1000).toFixed(1) + ' km';
        }

        /**
         * Formatea coordenadas para mostrar
         */
        function formatCoordinates(lat, lng) {
            return lat.toFixed(4) + ', ' + lng.toFixed(4);
        }

        /**
         * Actualiza la visualización de ubicación y distancia
         */
        function updateDisplay() {
            if (!state.userLocation) {
                userLocDisplay.textContent = '—';
                distanceDisplay.textContent = '—';
                return;
            }

            userLocDisplay.textContent = formatCoordinates(state.userLocation.lat, state.userLocation.lng);

            const distance = getDistanceMeters(
                state.userLocation.lat,
                state.userLocation.lng,
                config.lat,
                config.lng,
            );

            distanceDisplay.textContent = formatDistance(distance);
        }

        /**
         * Muestra un mensaje
         */
        function showMessage(text, type = 'success') {
            locationMessage.textContent = text;
            locationMessage.className = 'location-message location-message--' + type;

            if (type === 'success') {
                setTimeout(() => {
                    locationMessage.className = 'location-message';
                }, 3000);
            }
        }

        /**
         * Obtiene la ubicación actual del usuario
         */
        function locateUser() {
            if (!navigator.geolocation) {
                showMessage('Tu navegador no permite obtener la ubicación.', 'error');
                return;
            }

            showMessage('Obteniendo tu ubicación...', 'success');
            locateBtn.disabled = true;
            locateBtn.textContent = '⏳ Obteniendo ubicación...';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    state.userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };

                    updateDisplay();
                    showMessage('✓ Ubicación obtenida correctamente', 'success');
                    locateBtn.disabled = false;
                    locateBtn.innerHTML = '<i class="bi bi-crosshair2"></i> Obtener mi ubicación';
                },
                () => {
                    showMessage('No se pudo obtener tu ubicación. Revisa los permisos.', 'error');
                    locateBtn.disabled = false;
                    locateBtn.innerHTML = '<i class="bi bi-crosshair2"></i> Obtener mi ubicación';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                }
            );
        }

        // ── Event listeners ──
        if (locateBtn) {
            locateBtn.addEventListener('click', locateUser);
        }
    })();
