(function () {
    'use strict';

    const DATA = window.gimcanaData;
    if (!DATA) {
        console.error('Gimcana data not found');
        return;
    }

    // ── Elementos UI ──
    const els = {
        map: document.getElementById('map'),
        gpsOverlay: document.getElementById('gps-overlay'),
        btnStartGps: document.getElementById('btn-start-gps'),
        btnLocate: document.getElementById('btn-locate'),
        btnPermissions: document.getElementById('btn-permissions'),
        btnToggleDpad: document.getElementById('btn-toggle-dpad'),
        devDpad: document.getElementById('dev-dpad'),
        questionSheet: document.getElementById('question-sheet'),
        questionOverlay: document.getElementById('question-overlay'),
        answerForm: document.getElementById('answer-form'),
        answerInput: document.getElementById('answer-input'),
        answerError: document.getElementById('answer-error'),
        toast: document.getElementById('toast'),
        toastMessage: document.getElementById('toast-message'),
        // Detail Panel
        detailPanel: document.getElementById('detailPanel'),
        placeDetail: document.getElementById('placeDetail'),
        emptyState: document.getElementById('emptyState'),
        closeDetailBtn: document.getElementById('closeDetailButton'),
        detailImage: document.getElementById('detailImage'),
        detailName: document.getElementById('detailName'),
        detailCategory: document.getElementById('detailCategory'),
        detailDescription: document.getElementById('detailDescription'),
        detailAddress: document.getElementById('detailAddress'),
        detailCoordinates: document.getElementById('detailCoordinates'),
        detailDistanceRow: document.getElementById('detailDistanceRow'),
        detailDistance: document.getElementById('detailDistance'),
        routeButton: document.getElementById('routeButton'),
        centerButton: document.getElementById('centerButton')
    };

    // ── Estado ──
    const state = {
        map: null,
        userLocation: null,
        userMarker: null,
        watchId: null,
        markers: {},
        isNear: false,
        lastVibration: 0,
        audioContext: null,
        routingControl: null,
        selectedLugarId: null,
        activeRouteId: null, // ID del lugar hacia el que hay una ruta dibujada
        isFirstLocate: true,
        lastUpdate: 0, // Para throttling de 7s
        syncIntervalId: null
    };

    const DISTANCIA_PROXIMIDAD = 150; // Metros para activar la pregunta

    // ── Inicialización ──
    function init() {
        initMap();
        initEvents();
        renderAllPlaces();
        checkGpsPersistence();
    }

    function initMap() {
        state.map = L.map('map', {
            zoomControl: false, // Quitar zoom +/- por defecto
            attributionControl: false
        }).setView([DATA.retoActual.lat, DATA.retoActual.lng], 16);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 20
        }).addTo(state.map);
    }

    function checkGpsPersistence() {
        if (sessionStorage.getItem('gps_active') === 'true') {
            els.gpsOverlay.classList.add('hidden');
            initDeviceOrientation();
            startTracking();
        }
    }

    function initEvents() {
        els.btnStartGps.onclick = () => {
            sessionStorage.setItem('gps_active', 'true');
            els.gpsOverlay.classList.add('hidden');
            initDeviceOrientation();
            startTracking();
        };

        if (els.btnToggleDpad) {
            els.btnToggleDpad.onclick = () => els.devDpad.classList.toggle('hidden');
        }

        // Cerrar detalle
        if (els.closeDetailBtn) {
            els.closeDetailBtn.onclick = () => {
                els.detailPanel.classList.remove('is-visible');

                state.selectedLugarId = null;
            };
        }

        // Acciones detalle
        if (els.centerButton) {
            els.centerButton.onclick = () => {
                if (!state.selectedLugarId) return;
                const lugar = DATA.lugares.find(l => l.id === state.selectedLugarId);
                if (lugar) {
                    state.map.flyTo([lugar.latitud, lugar.longitud], 18);
                    els.closeDetailBtn.click();
                }
            };
        }

        if (els.routeButton) {
            els.routeButton.onclick = () => {
                if (!state.selectedLugarId) return;

                // Si ya hay una ruta para ESTE lugar, dejar de mostrarla
                if (state.activeRouteId === state.selectedLugarId) {
                    removeRoute();
                    els.routeButton.textContent = 'Mostrar ruta';
                    return;
                }

                if (!state.userLocation) {
                    showToast('Necesitamos tu ubicación para trazar la ruta');
                    return;
                }

                const lugar = DATA.lugares.find(l => l.id === state.selectedLugarId);
                if (lugar) {
                    drawRoute(lugar.id, lugar.latitud, lugar.longitud);
                    els.routeButton.textContent = 'Dejar de mostrar';
                }
            };
        }

        // D-Pad simulator
        const move = (dLat, dLng) => {
            if (!state.userLocation) {
                // Si no hay ubicación real, simular una inicial
                onLocationUpdate({
                    coords: {
                        latitude: DATA.retoActual.lat - 0.005,
                        longitude: DATA.retoActual.lng - 0.005,
                        accuracy: 10
                    }
                });
            }
            const newPos = {
                coords: {
                    latitude: state.userLocation.lat + dLat,
                    longitude: state.userLocation.lng + dLng,
                    accuracy: 10
                }
            };
            onLocationUpdate(newPos);
        };

        const STEP = 0.0001;
        const btnUp = document.getElementById('btn-up');
        if (btnUp) btnUp.onclick = () => move(STEP, 0);
        const btnDown = document.getElementById('btn-down');
        if (btnDown) btnDown.onclick = () => move(-STEP, 0);
        const btnLeft = document.getElementById('btn-left');
        if (btnLeft) btnLeft.onclick = () => move(0, -STEP);
        const btnRight = document.getElementById('btn-right');
        if (btnRight) btnRight.onclick = () => move(0, STEP);

        if (els.answerForm) {
            els.answerForm.onsubmit = async (e) => {
                e.preventDefault();
                await submitAnswer();
            };
        }

        // Cerrar panel al clicar en el mapa
        state.map.on('click', () => {
            if (els.detailPanel.classList.contains('is-visible')) {
                els.closeDetailBtn.click();
            }
        });

        // Botón de localización (ahora en el lateral)
        if (els.btnLocate) {
            els.btnLocate.onclick = () => {
                if (state.userLocation) {
                    state.map.flyTo([state.userLocation.lat, state.userLocation.lng], 17);
                }
            };
        }

        if (els.btnPermissions) {
            els.btnPermissions.onclick = () => {
                sessionStorage.setItem('gps_active', 'true');
                if (els.gpsOverlay) els.gpsOverlay.classList.add('hidden');
                initDeviceOrientation();
                startTracking();
                showToast('Permisos de ubicación activados');
            };
        }
    }

    function removeRoute() {
        if (state.routingControl) {
            state.map.removeControl(state.routingControl);
            state.routingControl = null;
            state.activeRouteId = null;
        }
    }

    function drawRoute(id, lat, lng) {
        removeRoute();

        state.routingControl = L.Routing.control({
            waypoints: [
                L.latLng(state.userLocation.lat, state.userLocation.lng),
                L.latLng(lat, lng)
            ],
            lineOptions: {
                styles: [{ color: '#0ea5a4', weight: 6, opacity: 0.8 }]
            },
            createMarker: () => null,
            addWaypoints: false,
            draggableWaypoints: false,
            fitSelectedRoutes: true,
            show: false
        }).on('routesfound', function(e) {
            const routes = e.routes;
            if (routes && routes.length > 0) {
                const distance = routes[0].summary.totalDistance;
                const time = Math.round(routes[0].summary.totalTime / 60);

                let distText = distance < 1000
                    ? `${Math.round(distance)} m`
                    : `${(distance / 1000).toFixed(2)} km`;


            }
        }).addTo(state.map);
        
        state.activeRouteId = id;
        showToast('Ruta trazada');
        els.detailPanel.classList.remove('is-visible');
        

    }

    // ── Seguimiento GPS ──
    function startTracking() {
        if (!navigator.geolocation) {
            showToast('El GPS no es compatible');
            return;
        }

        state.watchId = navigator.geolocation.watchPosition(
            onLocationUpdate,
            (err) => {
                console.error(err);

            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );

        if (!state.syncIntervalId) {
            state.syncIntervalId = window.setInterval(syncWithServer, 1000);
        }
    }

    function initDeviceOrientation() {
        if (typeof DeviceOrientationEvent !== 'undefined' && typeof DeviceOrientationEvent.requestPermission === 'function') {
            DeviceOrientationEvent.requestPermission()
                .then(permissionState => {
                    if (permissionState === 'granted') {
                        window.addEventListener('deviceorientation', handleOrientation);
                    }
                })
                .catch(console.error);
        } else {
            window.addEventListener('deviceorientationabsolute', handleOrientation);
            window.addEventListener('deviceorientation', handleOrientation);
        }
    }

    function handleOrientation(event) {
        let heading = null;
        if (event.webkitCompassHeading) {
            heading = event.webkitCompassHeading; // iOS
        } else if (event.absolute === true && event.alpha !== null) {
            heading = 360 - event.alpha; // Android
        } else if (event.alpha !== null) {
            heading = 360 - event.alpha; // Fallback
        }

        if (heading !== null) {
            const compassEl = document.getElementById('user-compass');
            if (compassEl) {
                // Keep the center aligned, only rotate
                compassEl.style.transform = `rotate(${heading}deg)`;
            }
        }
    }

    async function syncWithServer() {
        const payloadCoords = state.userLocation ? state.userLocation : { lat: 0, lng: 0 };
        
        try {
            const resp = await fetch(DATA.ubicacionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payloadCoords)
            });
            const result = await resp.json();
            if (result.gameOver && result.redirectUrl) {
                window.location.href = result.redirectUrl;
            }
        } catch (e) {
            console.error("Error actualizando ubicación en servidor:", e);
        }
    }

    async function onLocationUpdate(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        state.userLocation = { lat, lng };
        updateUserMarker(lat, lng);
        checkProximity();
        updateDistanceInDetail();

        // En la primera ubicación, centrar siempre en el usuario
        if (state.isFirstLocate) {
            state.map.flyTo([lat, lng], 17);
            state.isFirstLocate = false;
        }



        const now = Date.now();
        // Sincronizar locación con servidor para progreso del admin
        if (now - state.lastUpdate >= 7000) {
            state.lastUpdate = now;
            syncWithServer();
        }
    }

    function updateDistanceInDetail() {
        if (!state.selectedLugarId || !state.userLocation) return;
        const lugar = DATA.lugares.find(l => l.id === state.selectedLugarId);
        if (lugar) {
            const dist = getDistance(
                state.userLocation.lat, state.userLocation.lng,
                lugar.latitud, lugar.longitud
            );
            els.detailDistance.textContent = dist > 1000 
                ? `${(dist/1000).toFixed(2)} km` 
                : `${Math.round(dist)} metros`;
        }
    }

    function updateUserMarker(lat, lng) {
        if (!state.userMarker) {
            const icon = L.divIcon({
                className: 'custom-user-marker',
                html: '<div class="user-marker-container"><div class="user-compass" id="user-compass"></div><div class="user-dot"></div><div class="user-ring"></div></div>',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });
            state.userMarker = L.marker([lat, lng], { icon }).addTo(state.map);
        } else {
            state.userMarker.setLatLng([lat, lng]);
        }
    }

    function markerHtml(color, isActive, iconClass) {
        return `
            <div style="
                width: 24px;
                height: 24px;
                border-radius: 999px;
                background: ${color};
                color: #fff;
                border: 2px solid white;
                box-shadow: 0 4px 10px rgba(0,0,0,.2);
                transform: ${isActive ? 'scale(1.2)' : 'scale(1)'};
                transition: .2s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
            ">
                ${iconClass ? `<i class="${iconClass}"></i>` : ''}
            </div>
        `;
    }

    // ── Marcadores de Lugares ──
    function renderAllPlaces() {
        Object.values(state.markers).forEach(m => state.map.removeLayer(m));
        state.markers = {};

        DATA.lugares.forEach(lugar => {
            const isCurrent = (lugar.id === DATA.retoActual.id);
            
            const color = lugar.categoria?.color_marcador || '#0ea5a4';
            const iconClass = lugar.categoria?.icono_url || '';

            const marker = L.marker([lugar.latitud, lugar.longitud], {
                icon: L.divIcon({
                    className: 'custom-place-marker',
                    html: markerHtml(color, isCurrent, iconClass),
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                })
            }).addTo(state.map);

            marker.on('click', (e) => {
                L.DomEvent.stopPropagation(e);
                showPlaceDetail(lugar);
            });

            state.markers[lugar.id] = marker;
        });
    }

    function showPlaceDetail(lugar) {
        if (!lugar) return;
        state.selectedLugarId = lugar.id;
        
        // Asegurar que las coordenadas sean números
        const lat = parseFloat(lugar.latitud);
        const lng = parseFloat(lugar.longitud);
        
        if (els.detailName) els.detailName.textContent = lugar.nombre;
        
        if (els.detailCategory) {
            if (lugar.categoria) {
                const iconHtml = lugar.categoria.icono_url ? `<i class="${lugar.categoria.icono_url}" style="margin-right: 4px;"></i>` : '';
                els.detailCategory.innerHTML = `<span style="color: ${lugar.categoria.color_marcador || 'var(--primary)'}">
                    ${iconHtml}${lugar.categoria.nombre}
                </span>`;
            } else {
                els.detailCategory.textContent = 'Sin categoría';
            }
        }
        if (els.detailDescription) els.detailDescription.textContent = lugar.descripcion || 'Sin descripción disponible.';
        if (els.detailAddress) els.detailAddress.textContent = lugar.direccion_completa || 'No disponible';
        
        if (els.detailCoordinates) {
            els.detailCoordinates.textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
        }
        
        if (els.detailImage) {
            let imgName = lugar.imagen ? lugar.imagen : 'default_lugar.jpg';
            els.detailImage.src = `/img/lugares/${imgName}`;
            els.detailImage.classList.remove('hidden');
        }

        if (els.emptyState) els.emptyState.classList.add('hidden');
        if (els.placeDetail) els.placeDetail.classList.remove('hidden');
        if (els.detailPanel) els.detailPanel.classList.add('is-visible');

        
        if (els.detailDistanceRow) els.detailDistanceRow.classList.remove('hidden');

        // Toggle texto del botón de ruta
        if (els.routeButton) {
            els.routeButton.textContent = (state.activeRouteId === lugar.id) ? 'Dejar de mostrar' : 'Mostrar ruta';
        }

        updateDistanceInDetail();
    }

    // ── Lógica de Proximidad ──
    function checkProximity() {
        if (!state.userLocation) return;
        const dist = getDistance(
            state.userLocation.lat, state.userLocation.lng,
            DATA.retoActual.lat, DATA.retoActual.lng
        );

        if (dist <= DISTANCIA_PROXIMIDAD && !state.isNear) {
            state.isNear = true;
            triggerChallenge();
        } else if (dist > DISTANCIA_PROXIMIDAD + 50) {
            state.isNear = false;
        }
    }

    function triggerChallenge() {
        vibrate();
        playBeep('proximity');
        showToast('¡Has llegado al objetivo!');
        els.questionSheet.classList.add('active');
        els.questionOverlay.classList.add('active');
        els.answerInput.focus();
    }

    // ── Formulario de Respuesta ──
    async function submitAnswer() {
        const respuesta = els.answerInput.value.trim();
        if (!respuesta) return;
        els.answerError.classList.add('hidden');
        els.answerInput.disabled = true;

        try {
            const response = await fetch(DATA.resolverUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': DATA.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ respuesta: respuesta })
            });

            const result = await response.json();

            if (response.ok) {
                playBeep('success');
                showToast('¡Respuesta correcta!', 'success');
                setTimeout(() => {
                    window.location.href = result.redirect || '/gimcana/espera';
                }, 1000);
            } else {
                els.answerError.textContent = result.errors ? Object.values(result.errors)[0] : 'Respuesta incorrecta.';
                els.answerError.classList.remove('hidden');
                els.answerInput.disabled = false;
                vibrate([100, 50, 100]);
            }
        } catch (err) {
            console.error(err);
            els.answerInput.disabled = false;
            showToast('Error de conexión');
        }
    }

    // ── Utilidades ──
    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3;
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function showToast(msg) {
        els.toastMessage.textContent = msg;
        els.toast.classList.remove('hidden');
        setTimeout(() => els.toast.classList.add('hidden'), 3000);
    }

    function vibrate(pattern = [200]) {
        if (navigator.vibrate) navigator.vibrate(pattern);
        else playBeep();
    }

    function playBeep(type = 'default') {
        try {
            if (!state.audioContext) state.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            if (state.audioContext.state === 'suspended') state.audioContext.resume();

            const playNote = (freq, startTime, duration) => {
                const osc = state.audioContext.createOscillator();
                const gain = state.audioContext.createGain();
                osc.connect(gain);
                gain.connect(state.audioContext.destination);
                osc.frequency.setValueAtTime(freq, startTime);
                
                gain.gain.setValueAtTime(0.1, startTime);
                gain.gain.exponentialRampToValueAtTime(0.0001, startTime + duration);
                
                osc.start(startTime);
                osc.stop(startTime + duration);
            };

            const now = state.audioContext.currentTime;

            if (type === 'proximity') {
                // Dos pitidos ascendentes
                playNote(440, now, 0.1);
                playNote(880, now + 0.15, 0.2);
            } else if (type === 'success') {
                // Pequeña melodía de victoria
                playNote(523.25, now, 0.1); // C5
                playNote(659.25, now + 0.1, 0.1); // E5
                playNote(783.99, now + 0.2, 0.1); // G5
                playNote(1046.50, now + 0.3, 0.3); // C6
            } else {
                playNote(880, now, 0.2);
            }
        } catch(e){
            console.error('Audio error:', e);
        }
    }

    init();
})();
