<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bean origin map
    |--------------------------------------------------------------------------
    | Country centroids for the map on a bean page. Origin is stored as a
    | country name only, so this is the lookup that turns it into a point.
    | An unknown or empty origin falls back to a text-only origin block —
    | never to a world map centred on the ocean.
    |
    | zoom is the country view: 4 for large countries, 7 for small ones.
    */
    'origins' => [
        'Brazil' => ['lat' => -10.0, 'lng' => -52.0, 'zoom' => 4],
        'Colombia' => ['lat' => 4.0, 'lng' => -73.0, 'zoom' => 5],
        'Costa Rica' => ['lat' => 9.7, 'lng' => -84.2, 'zoom' => 7],
        'Ethiopia' => ['lat' => 9.1, 'lng' => 40.5, 'zoom' => 5],
        'Guatemala' => ['lat' => 15.5, 'lng' => -90.4, 'zoom' => 6],
        'Honduras' => ['lat' => 14.8, 'lng' => -86.6, 'zoom' => 6],
        'India' => ['lat' => 21.0, 'lng' => 78.0, 'zoom' => 4],
        'Indonesia' => ['lat' => -2.5, 'lng' => 118.0, 'zoom' => 4],
        'Kenya' => ['lat' => 0.2, 'lng' => 37.9, 'zoom' => 5],
        'Rwanda' => ['lat' => -1.9, 'lng' => 29.9, 'zoom' => 7],
        'Tanzania' => ['lat' => -6.4, 'lng' => 34.9, 'zoom' => 5],
        'Vietnam' => ['lat' => 16.0, 'lng' => 107.8, 'zoom' => 5],
    ],

    // CARTO light basemap, same client key as the cafe map. One line of
    // attribution is rendered under the map with it.
    'tiles' => 'https://basemaps.cartocdn.com/rastertiles/light_all/{z}/{x}/{y}.png?key=cb1_4zhm_1_bd5940b2878b3cd5f232c77c',
];
