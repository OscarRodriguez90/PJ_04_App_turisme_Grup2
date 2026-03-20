(() => {
    const data = window.geoTurismoData;

    if (!data) {
        return;
    }

    const state = {
        lugares: data.lugares || [],
        favoritosIds: new Set(data.favoritosIds || []),
        selectedLugarId: null,
        userLocation: null,
        markers: new Map(),
        routingControl: null,
        routeTargetId: null,
        isSidebarOpen: false,
        watchId: null,
        lastUpdate: 0,
    };

    const els = {
        map: document.getElementById('map'),
        placesList: document.getElementById('placesList'),
        visibleCount: document.getElementById('visibleCount'),
        favoritesCount: document.getElementById('favoritesCount'),
        resultsLabel: document.getElementById('resultsLabel'),
        radiusRange: document.getElementById('radiusRange'),
        radiusValue: document.getElementById('radiusValue'),
        favoritesOnly: document.getElementById('favoritesOnly'),
        nearbyOnly: document.getElementById('nearbyOnly'),
        searchInput: document.getElementById('searchInput'),
        categoryFilters: [...document.querySelectorAll('.category-filter')],
        locateMeButton: document.getElementById('locateMeButton'),
        toggleSidebar: document.getElementById('toggleSidebar'),
        closeSidebarBtn: document.getElementById('closeSidebarBtn'),
        sidebar: document.querySelector('.sidebar'),
        mapMessage: document.getElementById('mapMessage'),
        emptyState: document.getElementById('emptyState'),
        placeDetail: document.getElementById('placeDetail'),
        closeDetailButton: document.getElementById('closeDetailButton'),
        detailImage: document.getElementById('detailImage'),
        detailName: document.getElementById('detailName'),
        detailCategory: document.getElementById('detailCategory'),
        detailDescription: document.getElementById('detailDescription'),
        detailAddress: document.getElementById('detailAddress'),
        detailCoordinates: document.getElementById('detailCoordinates'),
        detailFavoriteButton: document.getElementById('detailFavoriteButton'),
        routeButton: document.getElementById('routeButton'),
        centerButton: document.getElementById('centerButton'),
        routeNote: document.getElementById('routeNote'),
        detailPanel: document.getElementById('detailPanel'),
        favOverlay: document.getElementById('favConfirmOverlay'),
        favConfirmIcon: document.getElementById('favConfirmIcon'),
        favConfirmTitle: document.getElementById('favConfirmTitle'),
        favConfirmText: document.getElementById('favConfirmText'),
        favConfirmOk: document.getElementById('favConfirmOk'),
        favConfirmCancel: document.getElementById('favConfirmCancel'),
    };

    const defaultCenter = [41.3597, 2.0997];
    const map = L.map(els.map, {
        zoomControl: false,
    }).setView(defaultCenter, 13);

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    const userMarker = L.circleMarker(defaultCenter, {
        radius: 0,
        color: '#0b6ef6',
        fillColor: '#0b6ef6',
        fillOpacity: 0.4,
    }).addTo(map);

    // ─── Helpers ─────────────────────────────────────────────────────────────

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function getLugarById(id) {
        return state.lugares.find((lugar) => lugar.id === id) || null;
    }

    function isFavorito(lugarId) {
        return state.favoritosIds.has(lugarId);
    }

    function getSelectedCategoryIds() {
        return els.categoryFilters
            .filter((checkbox) => checkbox.checked)
            .map((checkbox) => Number(checkbox.value));
    }

    function getDistanceMeters(lat1, lon1, lat2, lon2) {
        const toRad = (degrees) => (degrees * Math.PI) / 180;
        const R = 6371000;
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return Math.round(R * c);
    }

    /** Hace scroll suave hasta el mapa solo en responsive (< 1100 px). */
    function scrollToMapIfMobile() {
        if (window.innerWidth < 1100) {
            els.map.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    /** Hace scroll suave hasta el panel de detalle en responsive (< 1100 px). */
    function scrollToDetailIfMobile() {
        if (window.innerWidth < 1100 && els.detailPanel) {
            els.detailPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    /**
     * Muestra el panel de confirmación personalizado.
     * Devuelve una Promise<boolean>: true si confirma, false si cancela.
     */
    function mostrarPanelConfirmacion(titulo, texto, icono, esActivo) {
        return new Promise((resolve) => {
            els.favConfirmTitle.textContent = titulo;
            els.favConfirmText.textContent = texto;
            els.favConfirmIcon.textContent = icono;
            els.favConfirmIcon.style.color = esActivo ? 'var(--danger)' : 'var(--primary)';

            els.favOverlay.classList.add('is-visible');
            els.favOverlay.setAttribute('aria-hidden', 'false');

            function cerrar(resultado) {
                els.favOverlay.classList.remove('is-visible');
                els.favOverlay.setAttribute('aria-hidden', 'true');
                // Limpiar listeners para evitar acumulación
                els.favConfirmOk.removeEventListener('click', onOk);
                els.favConfirmCancel.removeEventListener('click', onCancel);
                els.favOverlay.removeEventListener('click', onOverlayClick);
                resolve(resultado);
            }

            function onOk() { cerrar(true); }
            function onCancel() { cerrar(false); }
            function onOverlayClick(e) {
                // Cerrar si se clica fuera del panel
                if (e.target === els.favOverlay) { cerrar(false); }
            }

            els.favConfirmOk.addEventListener('click', onOk);
            els.favConfirmCancel.addEventListener('click', onCancel);
            els.favOverlay.addEventListener('click', onOverlayClick);
        });
    }

    // ─── Filtros ──────────────────────────────────────────────────────────────

    function getFilteredLugares() {
        const search = els.searchInput.value.trim().toLowerCase();
        const categoryIds = new Set(getSelectedCategoryIds());
        const favoritesOnly = els.favoritesOnly.checked;
        const nearbyOnly = els.nearbyOnly.checked;
        const radius = Number(els.radiusRange.value);

        return state.lugares.filter((lugar) => {
            const matchesSearch = !search ||
                lugar.nombre.toLowerCase().includes(search) ||
                (lugar.direccion_completa || '').toLowerCase().includes(search);

            const matchesCategory = !lugar.categoria || categoryIds.has(lugar.categoria.id);
            const matchesFavorite = !favoritesOnly || isFavorito(lugar.id);

            let matchesNearby = true;
            if (nearbyOnly) {
                if (!state.userLocation) {
                    matchesNearby = false;
                } else {
                    const distance = getDistanceMeters(
                        state.userLocation.lat,
                        state.userLocation.lng,
                        lugar.latitud,
                        lugar.longitud,
                    );
                    matchesNearby = distance <= radius;
                }
            }

            return matchesSearch && matchesCategory && matchesFavorite && matchesNearby;
        });
    }

    // ─── Marcadores ───────────────────────────────────────────────────────────

    function markerHtml(color, active, iconClass) {
        return `
            <div style="
                width: 24px;
                height: 24px;
                border-radius: 999px;
                background: ${color};
                color: #fff;
                border: 2px solid white;
                box-shadow: 0 4px 10px rgba(0,0,0,.2);
                transform: ${active ? 'scale(1.2)' : 'scale(1)'};
                transition: .2s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
            ">
                ${iconClass ? `<i class="${escapeHtml(iconClass)}"></i>` : ''}
            </div>
        `;
    }

    function createMarker(lugar, active = false) {
        const marker = L.marker([lugar.latitud, lugar.longitud], {
            icon: L.divIcon({
                className: 'custom-place-marker',
                html: markerHtml(lugar.categoria?.color_marcador || '#0b6ef6', active, lugar.categoria?.icono_url),
                iconSize: [24, 24],
                iconAnchor: [12, 12],
            }),
        });

        marker.on('click', () => {
            state.selectedLugarId = lugar.id;
            render();
        });

        return marker;
    }

    function syncMarkers(filteredLugares) {
        const visibleIds = new Set(filteredLugares.map((lugar) => lugar.id));

        state.lugares.forEach((lugar) => {
            let marker = state.markers.get(lugar.id);
            if (!marker) {
                marker = createMarker(lugar, false);
                state.markers.set(lugar.id, marker);
            }

            const isVisible = visibleIds.has(lugar.id);
            const isActive = state.selectedLugarId === lugar.id;

            marker.setIcon(L.divIcon({
                className: 'custom-place-marker',
                html: markerHtml(lugar.categoria?.color_marcador || '#0b6ef6', isActive, lugar.categoria?.icono_url),
                iconSize: [24, 24],
                iconAnchor: [12, 12],
            }));

            if (isVisible && !map.hasLayer(marker)) {
                marker.addTo(map);
            }

            if (!isVisible && map.hasLayer(marker)) {
                map.removeLayer(marker);
            }
        });
    }

    // ─── Lista de lugares ─────────────────────────────────────────────────────

    function renderPlacesList(filteredLugares) {
        els.placesList.innerHTML = '';

        if (!filteredLugares.length) {
            els.placesList.innerHTML = '<div class="place-card"><strong>No hay resultados</strong><p>Prueba a cambiar los filtros o ampliar la distancia.</p></div>';
            return;
        }

        filteredLugares.forEach((lugar) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `place-card${state.selectedLugarId === lugar.id ? ' is-active' : ''}`;
            button.innerHTML = `
                <div class="place-card__top">
                    <div style="display:flex; gap:.75rem; align-items:flex-start;">
                        <span class="place-color" style="background:${escapeHtml(lugar.categoria?.color_marcador || '#0b6ef6')}">
                           ${lugar.categoria?.icono_url ? `<i class="${escapeHtml(lugar.categoria.icono_url)}" style="font-size: 13px;"></i>` : ''}
                        </span>
                        <div>
                            <h4>${escapeHtml(lugar.nombre)}</h4>
                            <p>${escapeHtml(lugar.descripcion || 'Sin descripción disponible.')}</p>
                        </div>
                    </div>
                    <strong>${isFavorito(lugar.id) ? '★' : '☆'}</strong>
                </div>
                <div class="place-card__bottom">
                    <small>${escapeHtml(lugar.categoria?.nombre || 'Sin categoría')}</small>
                    <small>${escapeHtml(lugar.direccion_completa || 'Dirección no disponible')}</small>
                </div>
            `;
            button.addEventListener('click', () => {
                state.selectedLugarId = lugar.id;
                render();
                map.flyTo([lugar.latitud, lugar.longitud], 16, { duration: 0.8 });

                // En responsive: cerrar sidebar y desplazarse al panel de detalle
                if (window.innerWidth < 1100) {
                    if (state.isSidebarOpen) {
                        state.isSidebarOpen = false;
                        els.sidebar?.classList.remove('is-open');
                        document.body.classList.remove('sidebar-is-open');
                    }
                    // Pequeño delay para que el DOM se actualice antes del scroll
                    setTimeout(() => scrollToDetailIfMobile(), 80);
                }
            });
            els.placesList.appendChild(button);
        });
    }

    // ─── Panel de detalle ─────────────────────────────────────────────────────

    function updateDetail(lugar) {
        if (!lugar) {
            els.emptyState.classList.remove('hidden');
            els.placeDetail.classList.add('hidden');
            return;
        }

        const wasHidden = els.placeDetail.classList.contains('hidden');

        els.emptyState.classList.add('hidden');
        els.placeDetail.classList.remove('hidden');

        // Animación de entrada solo si el panel estaba oculto anteriormente
        if (wasHidden) {
            els.placeDetail.classList.remove('is-entering');
            // Doble rAF para garantizar que el navegador detecta el cambio de clase
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    els.placeDetail.classList.add('is-entering');
                });
            });
        }

        els.detailName.textContent = lugar.nombre;

        let imgName = lugar.imagen ? lugar.imagen : 'default_lugar.jpg';
        els.detailImage.src = `/img/lugares/${imgName}`;

        if (lugar.categoria) {
            const iconHtml = lugar.categoria.icono_url ? `<i class="${escapeHtml(lugar.categoria.icono_url)}" style="margin-right: 4px;"></i>` : '';
            els.detailCategory.innerHTML = `<span style="color: ${escapeHtml(lugar.categoria.color_marcador || 'var(--primary)')}">
                ${iconHtml}${escapeHtml(lugar.categoria.nombre)}
            </span>`;
        } else {
            els.detailCategory.textContent = 'Sin categoría';
        }

        els.detailDescription.textContent = lugar.descripcion || 'Este lugar todavía no tiene descripción detallada.';
        els.detailAddress.textContent = lugar.direccion_completa || 'Dirección no disponible';
        els.detailCoordinates.textContent = `${lugar.latitud.toFixed(5)}, ${lugar.longitud.toFixed(5)}`;
        els.detailFavoriteButton.textContent = isFavorito(lugar.id) ? '♥' : '♡';
        els.detailFavoriteButton.classList.toggle('is-active', isFavorito(lugar.id));
        els.routeNote.textContent = state.userLocation
            ? 'Tu ubicación está activa. Ya puedes mostrar la ruta hasta este lugar.'
            : 'Necesitaremos tu ubicación actual para calcular la ruta.';

        updateDetailButtonState();
    }

    function updateCounters(filteredLugares) {
        els.visibleCount.textContent = String(filteredLugares.length);
        els.favoritesCount.textContent = String(state.favoritosIds.size);
        els.resultsLabel.textContent = `${filteredLugares.length} resultado${filteredLugares.length === 1 ? '' : 's'}`;
        els.radiusValue.textContent = els.radiusRange.value;
    }

    function updateDetailButtonState() {
        if (!els.routeButton) return;
        if (state.routingControl && state.routeTargetId === state.selectedLugarId) {
            els.routeButton.textContent = 'Dejar de mostrar la ruta';
            els.routeButton.classList.remove('btn-primary');
            els.routeButton.classList.add('btn-secondary');
        } else {
            els.routeButton.textContent = 'Mostrar ruta';
            els.routeButton.classList.remove('btn-secondary');
            els.routeButton.classList.add('btn-primary');
        }
    }

    // ─── Ruta ─────────────────────────────────────────────────────────────────

    function clearRoute() {
        if (state.routingControl) {
            map.removeControl(state.routingControl);
            state.routingControl = null;
        }
        state.routeTargetId = null;
        updateDetailButtonState();

        if (state.selectedLugarId) {
            els.routeNote.textContent = state.userLocation
                ? 'Tu ubicación está activa. Ya puedes mostrar la ruta hasta este lugar.'
                : 'Necesitaremos tu ubicación actual para calcular la ruta.';
        }
    }

    function drawRouteToSelected() {
        const lugar = getLugarById(state.selectedLugarId);
        if (!lugar) {
            els.mapMessage.textContent = 'Selecciona primero un lugar del mapa o de la lista.';
            return;
        }

        if (!state.userLocation) {
            locateUser(() => {
                drawRouteToSelected();
            });
            return;
        }

        clearRoute();

        state.routingControl = L.Routing.control({
            waypoints: [
                L.latLng(state.userLocation.lat, state.userLocation.lng),
                L.latLng(lugar.latitud, lugar.longitud),
            ],
            routeWhileDragging: false,
            addWaypoints: false,
            draggableWaypoints: false,
            fitSelectedRoutes: true,
            show: false,
            lineOptions: {
                styles: [{ color: '#0b6ef6', opacity: 0.85, weight: 5 }],
            },
            createMarker: () => null,
        }).on('routesfound', function(e) {
            const routes = e.routes;
            if (routes && routes.length > 0) {
                const distance = routes[0].summary.totalDistance;
                const time = Math.round(routes[0].summary.totalTime / 60);

                let distText = distance < 1000
                    ? `${Math.round(distance)} metros`
                    : `${(distance / 1000).toFixed(2)} km`;

                els.routeNote.innerHTML = `<span style="color:var(--primary); font-weight:600;">Distancia a la ruta: ${distText}</span> (Aprox. ${time} min)`;
            }
        }).addTo(map);

        state.routeTargetId = state.selectedLugarId;
        updateDetailButtonState();
        els.mapMessage.textContent = `Ruta calculada hasta ${lugar.nombre}.`;

        // En responsive, subir al mapa tras mostrar la ruta
        scrollToMapIfMobile();
    }

    // ─── Geolocalización ──────────────────────────────────────────────────────
    
    function startTracking() {
        if (!navigator.geolocation) {
            els.mapMessage.textContent = 'Tu navegador no permite obtener la ubicación actual.';
            return;
        }

        if (state.watchId) return;

        els.mapMessage.textContent = 'Iniciando seguimiento GPS...';

        state.watchId = navigator.geolocation.watchPosition(
            onLocationUpdate,
            (err) => {
                console.error('Error GPS:', err);
                els.mapMessage.textContent = 'Error obteniendo ubicación. Revisa los permisos.';
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0,
            }
        );
    }

    async function onLocationUpdate(position) {
        const now = Date.now();
        const shouldSync = (now - state.lastUpdate >= 7000);

        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        
        state.userLocation = { lat, lng };

        userMarker.setLatLng([lat, lng]);
        userMarker.setStyle({ radius: 10 });
        
        if (shouldSync) {
            state.lastUpdate = now;
            await syncLocationWithServer(lat, lng);
        }

        if (!state.selectedLugarId) {
             // map.flyTo([lat, lng], 14, { duration: 0.8 });
        }

        render();
    }

    async function syncLocationWithServer(lat, lng) {
        if (!data.actualizarUbicacionUrl) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            await fetch(data.actualizarUbicacionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({ lat, lng }),
            });
        } catch (error) {
            console.error('Error sincronizando ubicación:', error);
        }
    }

    function locateUser(onSuccess) {
        if (!state.watchId) {
            startTracking();
        } else if (state.userLocation) {
            map.flyTo([state.userLocation.lat, state.userLocation.lng], 14, { duration: 0.8 });
        }

        if (typeof onSuccess === 'function' && state.userLocation) {
            onSuccess();
        }
    }

    // ─── Favoritos ────────────────────────────────────────────────────────────

    async function toggleFavorito(lugarId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            const response = await fetch(`${data.toggleFavoritoUrl}/${lugarId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({}),
            });

            if (!response.ok) {
                throw new Error('No se pudo actualizar el favorito.');
            }

            const result = await response.json();
            state.favoritosIds = new Set(result.favoritos_ids || []);

            state.lugares = state.lugares.map((lugar) => ({
                ...lugar,
                is_favorito: state.favoritosIds.has(lugar.id),
            }));

            els.mapMessage.textContent = result.message || 'Favoritos actualizados.';
            render();
        } catch (error) {
            els.mapMessage.textContent = 'Ha ocurrido un error al actualizar favoritos.';
        }
    }

    // ─── Render principal ─────────────────────────────────────────────────────

    function render() {
        const filteredLugares = getFilteredLugares();
        const selectedLugarStillVisible = filteredLugares.some((lugar) => lugar.id === state.selectedLugarId);
        if (!selectedLugarStillVisible) {
            state.selectedLugarId = null;
        }

        syncMarkers(filteredLugares);
        renderPlacesList(filteredLugares);
        updateCounters(filteredLugares);
        updateDetail(getLugarById(state.selectedLugarId));
    }

    // ─── Event listeners ──────────────────────────────────────────────────────

    els.radiusRange.addEventListener('input', render);
    els.favoritesOnly.addEventListener('change', render);
    els.nearbyOnly.addEventListener('change', () => {
        if (els.nearbyOnly.checked && !state.userLocation) {
            locateUser(render);
        } else {
            render();
        }
    });
    els.searchInput.addEventListener('input', render);
    els.categoryFilters.forEach((checkbox) => checkbox.addEventListener('change', render));
    els.locateMeButton.addEventListener('click', () => locateUser(render));

    els.routeButton.addEventListener('click', () => {
        if (state.routingControl && state.routeTargetId === state.selectedLugarId) {
            clearRoute();
            els.mapMessage.textContent = 'La ruta se ha ocultado.';
        } else {
            drawRouteToSelected();
        }
    });

    els.centerButton.addEventListener('click', () => {
        const lugar = getLugarById(state.selectedLugarId);
        if (lugar) {
            map.flyTo([lugar.latitud, lugar.longitud], 16, { duration: 0.8 });
            // En responsive, subir al mapa tras centrar
            scrollToMapIfMobile();
        }
    });

    els.closeDetailButton?.addEventListener('click', () => {
        state.selectedLugarId = null;
        clearRoute();
        els.mapMessage.textContent = 'Has deseleccionado el lugar.';
        render();
    });

    // Confirmación antes de toggle favoritos (panel visual)
    els.detailFavoriteButton.addEventListener('click', async () => {
        if (!state.selectedLugarId) return;

        const esFav = isFavorito(state.selectedLugarId);
        const lugar = getLugarById(state.selectedLugarId);
        const nombre = lugar ? lugar.nombre : 'este lugar';        

        const titulo = esFav ? '¿Quitar de favoritos?' : '¿Añadir a favoritos?';
        const texto = esFav
            ? `Se eliminará "${nombre}" de tu lista de favoritos.`
            : `Se añadirá "${nombre}" a tu lista de favoritos.`;
        const icono = esFav ? '♥' : '♡';

        // Actualizar texto del botón Confirmar
        els.favConfirmOk.textContent = esFav ? 'Sí, quitar' : 'Sí, añadir';

        const confirmado = await mostrarPanelConfirmacion(titulo, texto, icono, esFav);
        if (confirmado) {
            toggleFavorito(state.selectedLugarId);
        }
    });

    els.toggleSidebar?.addEventListener('click', () => {
        state.isSidebarOpen = !state.isSidebarOpen;
        els.sidebar?.classList.toggle('is-open', state.isSidebarOpen);
        document.body.classList.toggle('sidebar-is-open', state.isSidebarOpen);
    });

    els.closeSidebarBtn?.addEventListener('click', () => {
        state.isSidebarOpen = false;
        els.sidebar?.classList.remove('is-open');
        document.body.classList.remove('sidebar-is-open');
    });

    render();
    startTracking();
})();
