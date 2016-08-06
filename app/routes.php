<?php
// map homepage
$route->map( 'GET', '/','index@HomeController');

$route->map( 'POST', '/account/password/reset','passwordResetFinal@Auth\AuthController');
$route->map( 'POST', '/account/password/reset/view','resetPassword@Auth\AuthController');
$route->map( 'POST', '/authenticate','authenticate@Auth\AuthController');
$route->map( 'POST', '/signup','createUser@Auth\AuthController');
$route->map( 'GET', '/signup','signup@Auth\AuthController');
$route->map( 'GET', '/account/created','accountCreated@Auth\AuthController');
$route->map( 'GET', '/account/activate/[*:token]','accountActivate@Auth\AuthController');
$route->map( 'GET', '/account/password/reset','getResetPassword@Auth\AuthController');
$route->map( 'GET', '/account/password/reset/[*:token]','passwordReset@Auth\AuthController');
$route->map( 'GET', '/login','login@Auth\AuthController');
$route->map( 'GET', '/logout','logout@Auth\AuthController');