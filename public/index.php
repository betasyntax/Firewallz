<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

// require dirname(__FILE__) . '/../boot/app.new.php';
// 

require __DIR__.'/../vendor/autoload.php';
echo "<pre>";
$app = require_once realpath(__DIR__.'/../boot/app.new.php');
// $app->make();

// var_dump($app);

// $test = $app->get('view');

// var_dump($test);
echo "</pre>";