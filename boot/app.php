<?php

/**
 * define the application root path on the webserver
 * @var string
 */

define('APP_ROOT', dirname(__FILE__) . '/../');

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
*/

require APP_ROOT.'vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Start the bootstrap
|--------------------------------------------------------------------------
*/

require APP_ROOT.'boot/bootstrap.inc.php';

require APP_ROOT.'app/routes.php';

require APP_ROOT.'app/helpers.php';

$route->dispatch();
