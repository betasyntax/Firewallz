<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';

$app = require_once realpath(__DIR__.'/../boot/app.new.php');

// $router = $app->container->get('Betasyntax\Router');

// require $app->getBasePath().'/app/routes.php';

// $router->dispatch();