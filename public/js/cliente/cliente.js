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
        isSidebarOpen: false,
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
        sidebar: document.querySelector('.sidebar'),
        mapMessage: document.getElementById('mapMessage'),
        emptyState: document.getElementById('emptyState'),
        placeDetail: document.getElementById('placeDetail'),
        detailName: document.getElementById('detailName'),
        detailCategory: document.getElementById('detailCategory'),
        detailDescription: document.getElementById('detailDescription'),
        detailAddress: document.getElementById('detailAddress'),
        detailCoordinates: document.getElementById('detailCoordinates'),
        detailFavoriteButton: document.getElementById('detailFavoriteButton'),
        routeButton: document.getElementById('routeButton'),
        centerButton: document.getElementById('centerButton'),
        routeNote: document.getElementById('routeNote'),
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

    function markerHtml(color, active) {
        return `
            <div style="
                width: 18px;
                height: 18px;
                border-radius: 999px;
                background: ${color};
                border: 3px solid white;
                box-shadow: 0 8px 18px rgba(20,33,61,.22);
                transform: ${active ? 'scale(1.15)' : 'scale(1)'};
                transition: .2s ease;
            "></div>
        `;
    }

    function createMarker(lugar, active = false) {
        const marker = L.marker([lugar.latitud, lugar.longitud], {
            icon: L.divIcon({
                className: 'custom-place-marker',
                html: markerHtml(lugar.categoria?.color_marcador || '#0b6ef6', active),
                iconSize: [18, 18],
                iconAnchor: [9, 9],
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
                html: markerHtml(lugar.categoria?.color_marcador || '#0b6ef6', isActive),
                iconSize: [18, 18],
                iconAnchor: [9, 9],
            }));

            if (isVisible && !map.hasLayer(marker)) {
                marker.addTo(map);
            }

            if (!isVisible && map.hasLayer(marker)) {
                map.removeLayer(marker);
            }
        });
    }

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
                        <span class="place-color" style="background:${escapeHtml(lugar.categoria?.color_marcador || '#0b6ef6')}"></span>
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
            });
            els.placesList.appendChild(button);
        });
    }

    function updateDetail(lugar) {
        if (!lugar) {
            els.emptyState.classList.remove('hidden');
            els.placeDetail.classList.add('hidden');
            return;
        }

        els.emptyState.classList.add('hidden');
        els.placeDetail.classList.remove('hidden');
        els.detailName.textContent = lugar.nombre;
        els.detailCategory.textContent = lugar.categoria?.nombre || 'Sin categoría';
        els.detailDescription.textContent = lugar.descripcion || 'Este lugar todavía no tiene descripción detallada.';
        els.detailAddress.textContent = lugar.direccion_completa || 'Dirección no disponible';
        els.detailCoordinates.textContent = `${lugar.latitud.toFixed(5)}, ${lugar.longitud.toFixed(5)}`;
        els.detailFavoriteButton.textContent = isFavorito(lugar.id) ? '♥' : '♡';
        els.detailFavoriteButton.classList.toggle('is-active', isFavorito(lugar.id));
        els.routeNote.textContent = state.userLocation
            ? 'Tu ubicación está activa. Ya puedes mostrar la ruta hasta este lugar.'
            : 'Necesitaremos tu ubicación actual para calcular la ruta.';
    }

    function updateCounters(filteredLugares) {
        els.visibleCount.textContent = String(filteredLugares.length);
        els.favoritesCount.textContent = String(state.favoritosIds.size);
        els.resultsLabel.textContent = `${filteredLugares.length} resultado${filteredLugares.length === 1 ? '' : 's'}`;
        els.radiusValue.textContent = els.radiusRange.value;
    }

    function clearRoute() {
        if (state.routingControl) {
            map.removeControl(state.routingControl);
            state.routingControl = null;
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
        }).addTo(map);

        els.mapMessage.textContent = `Ruta calculada hasta ${lugar.nombre}.`;
    }

    function locateUser(onSuccess) {
        if (!navigator.geolocation) {
            els.mapMessage.textContent = 'Tu navegador no permite obtener la ubicación actual.';
            return;
        }

        els.mapMessage.textContent = 'Obteniendo tu ubicación actual...';

        navigator.geolocation.getCurrentPosition((position) => {
            state.userLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
            };

            userMarker.setLatLng([state.userLocation.lat, state.userLocation.lng]);
            userMarker.setStyle({ radius: 10 });
            map.flyTo([state.userLocation.lat, state.userLocation.lng], 14, { duration: 0.8 });
            els.mapMessage.textContent = 'Ubicación obtenida correctamente.';
            render();

            if (typeof onSuccess === 'function') {
                onSuccess();
            }
        }, () => {
            els.mapMessage.textContent = 'No se pudo obtener tu ubicación. Revisa los permisos del navegador.';
        }, {
            enableHighAccuracy: true,
            timeout: 10000,
        });
    }

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

    function render() {
        const filteredLugares = getFilteredLugares();
        const selectedLugarStillVisible = filteredLugares.some((lugar) => lugar.id === state.selectedLugarId);
        if (!selectedLugarStillVisible) {
            state.selectedLugarId = filteredLugares[0]?.id ?? null;
        }

        syncMarkers(filteredLugares);
        renderPlacesList(filteredLugares);
        updateCounters(filteredLugares);
        updateDetail(getLugarById(state.selectedLugarId));
    }

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
    els.routeButton.addEventListener('click', drawRouteToSelected);
    els.centerButton.addEventListener('click', () => {
        const lugar = getLugarById(state.selectedLugarId);
        if (lugar) {
            map.flyTo([lugar.latitud, lugar.longitud], 16, { duration: 0.8 });
        }
    });
    els.detailFavoriteButton.addEventListener('click', () => {
        if (state.selectedLugarId) {
            toggleFavorito(state.selectedLugarId);
        }
    });
    els.toggleSidebar?.addEventListener('click', () => {
        state.isSidebarOpen = !state.isSidebarOpen;
        els.sidebar?.classList.toggle('is-open', state.isSidebarOpen);
    });

    render();
})();
