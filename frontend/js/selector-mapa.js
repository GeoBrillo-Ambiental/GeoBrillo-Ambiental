function montarSelectorMapa(idDiv, inputLat, inputLng) {
    const mapa = L.map(idDiv).setView([-34.9011, -56.1645], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> colaboradores'
    }).addTo(mapa);

    let marcador = null;

    function desdeInputs() {
        const lat = parseFloat(inputLat.value), lng = parseFloat(inputLng.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            if (marcador) { marcador.setLatLng([lat, lng]); }
            else { marcador = L.marker([lat, lng]).addTo(mapa); }
            mapa.setView([lat, lng], Math.max(mapa.getZoom(), 15));
        } else if (marcador) {
            mapa.removeLayer(marcador);
            marcador = null;
        }
    }

    mapa.on('click', function (e) {
        inputLat.value = e.latlng.lat.toFixed(6);
        inputLng.value = e.latlng.lng.toFixed(6);
        desdeInputs();
    });

    inputLat.addEventListener('change', desdeInputs);
    inputLng.addEventListener('change', desdeInputs);

    if (inputLat.form) {
        inputLat.form.addEventListener('reset', () => setTimeout(desdeInputs, 0));
    }

    return {
        refrescar() { mapa.invalidateSize(); desdeInputs(); }
    };
}

