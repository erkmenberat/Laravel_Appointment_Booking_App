<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Zeitstempel für Performance-/Startzeitmessung.
define('LARAVEL_START', microtime(true));

// Prüft, ob die Anwendung im Wartungsmodus läuft.
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Composer-Autoloader laden.
require __DIR__.'/../vendor/autoload.php';

// Laravel booten und HTTP-Request verarbeiten.
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
