import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Bean origin map (/beans/{slug}) — one country view, one pin. Leaflet
// loads only on this page through its own Vite entry, exactly like the
// cafe map, so the catalog stays free of the ~60 kB library.

const boot = window.BEAN_ORIGIN;
const el = document.getElementById('bean-origin-map');

if (boot && el) {
    const map = L.map(el, {
        center: [boot.lat, boot.lng],
        zoom: boot.zoom,
        scrollWheelZoom: false,
        // On touch a draggable map swallows the page scroll; the country
        // view and the zoom buttons carry the meaning instead.
        dragging: !L.Browser.mobile,
        touchZoom: !L.Browser.mobile,
    });

    L.tileLayer(boot.tiles, {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
    }).addTo(map);

    // Same pin geometry as the cafe map, drawn inline so no image request
    // and no marker-icon path config is needed.
    const pin = L.divIcon({
        html: '<svg width="26" height="34" viewBox="0 0 26 34" aria-hidden="true">'
            + '<path d="M13 1.5c-6.1 0-11 4.8-11 10.8C2 20.5 13 32.5 13 32.5s11-12 11-20.2c0-6-4.9-10.8-11-10.8Z" fill="currentColor"/>'
            + '<circle cx="13" cy="12.3" r="4.3" fill="var(--color-coffee-latte)"/></svg>',
        className: 'bean-pin',
        iconSize: [26, 34],
        iconAnchor: [13, 33],
    });

    L.marker([boot.lat, boot.lng], {
        icon: pin,
        alt: boot.label,
        keyboard: false,
    }).addTo(map);
}
