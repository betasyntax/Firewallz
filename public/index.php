<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>";
// require dirname(__FILE__) . '/../boot/app.new.php';
// 

require __DIR__.'/../vendor/autoload.php';
$app = require_once realpath(__DIR__.'/../boot/app.new.php');
var_dump($app);
echo "</pre>";