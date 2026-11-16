import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            // cafes.js is a separate entry: Leaflet + markercluster (~60 kB)
            // load only on /cafes, not on every page. bean-map.js is the
            // same idea for the single-country map on a bean page.
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/cafes.js', 'resources/js/bean-map.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
