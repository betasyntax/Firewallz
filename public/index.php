<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('xdebug.show_exception_trace', 0);

require __DIR__.'/../vendor/autoload.php';

$app = require_once realpath(__DIR__.'/../boot/app.php');

$router = $app->container->get('Betasyntax\Router');

$router->dispatch();