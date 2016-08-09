<?php
// map homepage
// 
// 

use Betasyntax\Core\Application;

$app = new Betasyntax\Core\Application;
$app = $app::getInstance();



$router = $app->container->get('Betasyntax\Router');


$router->map( 'GET', '/','index@HomeControllers');

$router->map( 'POST', '/account/password/reset','passwordResetFinal@Auth\AuthController');
$router->map( 'POST', '/account/password/reset/view','resetPassword@Auth\AuthController');
$router->map( 'POST', '/authenticate','authenticate@Auth\AuthController');
$router->map( 'POST', '/signup','createUser@Auth\AuthController');
$router->map( 'GET', '/signup','signup@Auth\AuthController');
$router->map( 'GET', '/account/created','accountCreated@Auth\AuthController');
$router->map( 'GET', '/account/activate/[*:token]','accountActivate@Auth\AuthController');
$router->map( 'GET', '/account/password/reset','getResetPassword@Auth\AuthController');
$router->map( 'GET', '/account/password/reset/[*:token]','passwordReset@Auth\AuthController');
$router->map( 'GET', '/login','login@Auth\AuthController');
$router->map( 'GET', '/logout','logout@Auth\AuthController');

$router->dispatch();