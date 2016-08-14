<?php 
error_reporting(E_ALL);
ini_set('xdebug.show_exception_trace', 0);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require __DIR__.'/../vendor/autoload.php';

$app = require_once realpath(__DIR__.'/../boot/app.php');
$app->router->dispatch();