<?php 
error_reporting(E_ALL);
ini_set('xdebug.show_exception_trace', 0);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set('xdebug.trace_format',2);
ini_set("html_errors", 1); 
ini_set("error_prepend_string", "<pre style='color: #333; font-face:monospace; font-size:8pt;'>"); 
ini_set("error_append_string ", "</pre>"); 

require __DIR__.'/../vendor/autoload.php';

$app = require_once realpath(__DIR__.'/../boot/app.php');
$app->router->dispatch();