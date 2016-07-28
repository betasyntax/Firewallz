<?php
// namespace Lib;
use Lib\Wayfinder;
use Lib\Registry;
use Lib\Router;
use Lib\Config;
use Lib\Response;
use Lib\Session;
use Lib\Authentication;
use Lib\Functions;
use Lib\MetaTrait;

function app(){
  global $registry;
  return $registry;
}

$registry = new Registry();
$route = new Router();
$config = new Config();

$haml = new MtHaml\Environment('twig');

$twig = new Twig_Loader_Filesystem(array(APP_ROOT.'app/Views/'));
$hamll = new MtHaml\Support\Twig\Loader($haml, $twig);

app()->twig = new Twig_Environment($hamll);
app()->twig->addExtension(new MtHaml\Support\Twig\Extension());


app()->session = Session::getInstance();
app()->flash = new Plasticbrain\FlashMessages\FlashMessages();
app()->util = new Functions();
app()->auth = new Authentication();
app()->response = new Response();