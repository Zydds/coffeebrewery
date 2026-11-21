<?php

// Vercel serves this file as /api/index.php, so Symfony's base-URL detection
// treats "/api" as the app base and strips it from every request path. That
// would turn /api/cafes into /cafes and break every JSON route.
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

require __DIR__.'/../public/index.php';
