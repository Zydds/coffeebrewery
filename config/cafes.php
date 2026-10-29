<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Overpass API instances
    |--------------------------------------------------------------------------
    | The importer cycles through these with retry/backoff. The main
    | instance (overpass-api.de) is unreachable from the dev machine (TLS
    | handshake fails), and mirrors rate-limit or 504 under load — so a
    | single hardcoded instance would make the import flaky by design.
    | Order matters: working instances first.
    */
    'overpass_instances' => [
        'https://maps.mail.ru/osm/tools/overpass/api/interpreter',
        'https://overpass-api.de/api/interpreter',
        'https://overpass.kumi.systems/api/interpreter',
    ],

    // Tries per instance before moving to the next one.
    'overpass_retries_per_instance' => 3,

    // Linear backoff base in seconds (5, 10, 15... between tries).
    'overpass_backoff_seconds' => 5,

    'overpass_timeout_seconds' => 180,

    // Overpass's usage policy requires an identifying User-Agent; requests
    // without one are rejected with HTTP 406. Keep the contact reachable.
    'user_agent' => 'Coffeebrewery (cafe map import) — contact: https://coffee.zaidanghiffari.my.id',

    /*
    |--------------------------------------------------------------------------
    | Default pilot region
    |--------------------------------------------------------------------------
    | Shown when the /cafes page loads with no ?region= in the URL.
    | Kode wilayah of Kota Jakarta Selatan.
    */
    'default_region' => '31.74',

    /*
    |--------------------------------------------------------------------------
    | Area assignment
    |--------------------------------------------------------------------------
    | At import, each café is assigned to the nearest OSM place node within
    | this radius (metres). Wider = sloppier but fewer unassigned cafés.
    | The audit measured place-node density in Jakarta Selatan; 1km matched
    | kelurahan-level granularity there.
    */
    'area_radius_m' => 1000,

    // Hard cap on cafés returned per map API call (city zoom = hundreds).
    'api_row_limit' => 500,
];
