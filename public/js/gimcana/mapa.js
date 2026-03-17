(function () {
    'use strict';

    const config = window.gimcanaMapaData;
    const mapNode = document.getElementById('reto-map');

    if (!config || !mapNode || typeof L === 'undefined') {
        return;
    }

    const map = L.map(mapNode, {
        zoomControl: true,
        dragging: true,
    }).setView([config.lat, config.lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    L.marker([config.lat, config.lng])
        .addTo(map)
        .bindPopup(config.nombre)
        .openPopup();
}());
