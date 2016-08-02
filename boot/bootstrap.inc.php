<?php
// namespace Lib;
use Betasyntax\Wayfinder;
use Betasyntax\Registry;
use Betasyntax\Router;
use Betasyntax\Config;
use Betasyntax\Response;
use Betasyntax\Session;
use Betasyntax\Authentication;
use Betasyntax\Functions;
use Betasyntax\MetaTrait;
use Plasticbrain\FlashMessages\FlashMessages;
use MtHaml\Environment as HamlEnv;
use MtHaml\Support\Twig\Loader as HamlLoader;
use MtHaml\Support\Twig\Extension as HamlExt;

function app(){
  global $registry;
  return $registry;
}

$registry = new Registry();
$route = new Router();
$dbconfig = new Config();

$haml = new HamlEnv('twig');
$twig = new Twig_Loader_Filesystem(array(APP_ROOT.'app/Views/'));
$hamll = new HamlLoader($haml, $twig);

app()->twig = new Twig_Environment($hamll);
app()->twig->addExtension(new HamlExt());

app()->session = Session::getInstance();
app()->flash = new FlashMessages();
app()->util = new Functions();
app()->auth = new Authentication();
app()->response = new Response();