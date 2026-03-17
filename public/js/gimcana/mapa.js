(function () {
    'use strict';

    const mapNode = document.getElementById('reto-map');
    const locateMeButton = document.getElementById('locateMeButton');
    const distanceDisplay = document.getElementById('distanceDisplay');
    const mapMessage = document.getElementById('mapMessage');

    const config = mapNode
        ? {
            lat: Number.parseFloat(mapNode.dataset.lat || '0'),
            lng: Number.parseFloat(mapNode.dataset.lng || '0'),
            nombre: mapNode.dataset.nombre || 'Destino',
        }
        : null;

    if (!config || !mapNode || Number.isNaN(config.lat) || Number.isNaN(config.lng) || typeof L === 'undefined') {
        return;
    }

    // ── Estado ──
    const state = {
        userLocation: null,
        routingControl: null,
        userMarker: null,
        watchId: null,
    };

    // ── Inicializar mapa ──
    const map = L.map(mapNode, {
        zoomControl: true,
        dragging: true,
    }).setView([config.lat, config.lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    // ── Marcador del destino ──
    L.marker([config.lat, config.lng], {
        icon: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        })
    })
        .addTo(map)
        .bindPopup(config.nombre)
        .openPopup();

    // ── Marcador del usuario (círculo azul) ──
    const userMarker = L.circleMarker([config.lat, config.lng], {
        radius: 0,
        color: '#0b6ef6',
        fillColor: '#0b6ef6',
        fillOpacity: 0.75,
        weight: 2,
    }).addTo(map);
    state.userMarker = userMarker;

    function getGeoErrorMessage(error) {
        if (!error) {
            return 'No se pudo obtener tu ubicación.';
        }

        if (error.code === error.PERMISSION_DENIED) {
            return 'Permiso de ubicación denegado. Actívalo en el navegador.';
        }

        if (error.code === error.POSITION_UNAVAILABLE) {
            return 'Ubicación no disponible. Revisa GPS o red.';
        }

        if (error.code === error.TIMEOUT) {
            return 'Tiempo de espera agotado al obtener tu ubicación.';
        }

        return 'No se pudo obtener tu ubicación.';
    }

    function applyUserLocation(position) {
        state.userLocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
        };

        // Actualizar marcador del usuario
        userMarker.setLatLng([state.userLocation.lat, state.userLocation.lng]);
        userMarker.setStyle({ radius: 11 });

        // Centrar mapa en la ubicación del usuario
        map.flyTo([state.userLocation.lat, state.userLocation.lng], 15, { duration: 0.8 });

        updateDistance();
        showMessage('Ubicación obtenida correctamente.', 'success');

        // Auto-dibujar ruta después de 1 segundo
        setTimeout(() => {
            drawRoute();
        }, 1000);
    }

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
            return meters + ' metros';
        }
        return (meters / 1000).toFixed(1) + ' km';
    }

    /**
     * Actualiza la visualización de distancia
     */
    function updateDistance() {
        if (!state.userLocation) {
            distanceDisplay.textContent = 'Obtén tu ubicación';
            return;
        }

        const distance = getDistanceMeters(
            state.userLocation.lat,
            state.userLocation.lng,
            config.lat,
            config.lng,
        );

        distanceDisplay.textContent = formatDistance(distance);
        distanceDisplay.title = distance + ' metros';
    }

    /**
     * Obtiene la ubicación actual del usuario
     */
    function locateUser(onSuccess) {
        if (!navigator.geolocation) {
            showMessage('Tu navegador no permite obtener la ubicación actual.', 'error');
            return;
        }

        if (!window.isSecureContext) {
            showMessage('La geolocalización requiere HTTPS o localhost.', 'error');
            return;
        }

        showMessage('Obteniendo tu ubicación...', 'info');

        navigator.geolocation.getCurrentPosition(
            (position) => {
                applyUserLocation(position);

                if (typeof onSuccess === 'function') {
                    onSuccess();
                }
            },
            (error) => {
                showMessage(getGeoErrorMessage(error), 'error');
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0,
            }
        );
    }

    function startLocationWatch() {
        if (!navigator.geolocation || !window.isSecureContext || state.watchId !== null) {
            return;
        }

        state.watchId = navigator.geolocation.watchPosition(
            (position) => {
                if (!state.userLocation) {
                    applyUserLocation(position);
                    return;
                }

                state.userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };
                userMarker.setLatLng([state.userLocation.lat, state.userLocation.lng]);
                updateDistance();
            },
            () => {
                // Si falla el watch, dejamos la última ubicación conocida sin bloquear el flujo.
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 3000,
            }
        );
    }

    /**
     * Dibuja la ruta desde el usuario al destino
     */
    function drawRoute() {
        if (!state.userLocation) {
            locateUser(() => {
                drawRoute();
            });
            return;
        }

        // Eliminar ruta anterior si existe
        if (state.routingControl) {
            map.removeControl(state.routingControl);
        }

        state.routingControl = L.Routing.control({
            waypoints: [
                L.latLng(state.userLocation.lat, state.userLocation.lng),
                L.latLng(config.lat, config.lng),
            ],
            router: L.Routing.osrmv1({
                serviceUrl: 'https://router.project-osrm.org/route/v1'
            }),
            lineOptions: {
                styles: [{ color: '#11a8ad', opacity: 0.7, weight: 4 }],
                extendToWaypoints: true,
                missingRouteTolerance: 2
            },
            createMarker: () => null, // No mostrar marcadores adicionales
            collapsedClassName: 'collapsed',
            position: 'topright',
            show: false,
        }).addTo(map);

        // Ajustar zoom a la ruta
        setTimeout(() => {
            map.fitBounds(state.routingControl.getPlan().getWaypoints()
                .map(wp => wp.latLng).filter(latLng => latLng), { padding: [50, 50] });
        }, 100);
    }

    /**
     * Muestra un mensaje en la interfaz
     */
    function showMessage(text, type = 'info') {
        mapMessage.textContent = text;
        mapMessage.className = 'map-message map-message--' + type;
        mapMessage.style.display = 'block';

        if (type === 'success' || type === 'info') {
            setTimeout(() => {
                mapMessage.style.display = 'none';
            }, 4000);
        }
    }

    // ── Event listeners ──
    if (locateMeButton) {
        locateMeButton.addEventListener('click', () => {
            locateUser();
        });
    }

    // Intentar autoubicación al abrir la pantalla para mostrar posición cuanto antes.
    locateUser();
    startLocationWatch();

}());
