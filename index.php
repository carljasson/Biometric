<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is In Maintenance Mode
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" artisan
| command, we will load this file so that any pre-rendered content can
| be shown instead of starting the full Laravel application.
|
*/
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Composer Autoloader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We will simply require it into the script here
| so that we do not have to manually load our classes.
|
*/
require __DIR__.'/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then we will send the response back
| to this client's browser and terminate the request properly.
|
*/
/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$response = $app->handle(
    $request = Request::capture()
);

$response->send();

$app->terminate($request, $response);
